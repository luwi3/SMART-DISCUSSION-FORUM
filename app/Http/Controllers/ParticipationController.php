<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Topic;
use App\Models\Student;
use App\Models\Quiz;
use App\Models\TopicParticipation;
use App\Models\Message;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class ParticipationController extends Controller
{
    /**
     * 👨‍🏫 Display the dynamic, system-automated Topic Participation Grade Matrix for Lecturers.
     */
    public function index()
    {
        // 1. Fetch all active topics ordered by creation date
        $topics = Topic::orderBy('created_at', 'asc')->get();

        // 2. 🛡️ STRICT WHITELIST: Only pull users whose role column is exactly 'student'
        $students = User::where('role', 'student')
                        ->orderBy('name', 'asc')
                        ->get();

        // 3. Map student message metrics directly into a look-up grid matrix layout
        $matrix = [];

        foreach ($students as $student) {
            foreach ($topics as $topic) {
                // Count rows using your exact message database keys
                $messageCount = Message::where('topic_id', $topic->id)
                                       ->where('user_id', $student->id)
                                       ->count();

                // 🧮 SYSTEM ALGORITHM: Each reply adds 2 points, capped at 20 marks total per topic
                $calculatedScore = $messageCount * 2;
                if ($calculatedScore > 20) {
                    $calculatedScore = 20;
                }

                $matrix[$student->id][$topic->id] = $calculatedScore;

                // Sync data down to your persistent tracking table
                TopicParticipation::updateOrCreate(
                    [
                        'topic_id' => $topic->id,
                        'user_id'  => $student->id
                    ],
                    [
                        'marks_earned' => $calculatedScore
                    ]
                );
            }
        }

        return view('participation.index', compact('topics', 'students', 'matrix'));
    }

    /**
     * 🎓 Render Student Dashboard with synchronized Forum Participation Marks, Dynamic Tabs,
     * and Quiz Lockdown state.
     */
    public function studentDashboard(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect('/login')->with('error', 'Please log in to view your dashboard.');
        }

        // 1. Get the student's profile record — regNo is the key quiz_submissions uses,
        //    NOT user_id, so every quiz-related lookup below goes through $student->regNo.
        $student = Student::where('user_id', $userId)->first();
        $studentCourse = $student ? $student->courseCode : null;
        $studentRegNo  = $student ? $student->regNo : null;

        // ➕ ADDED: expose the student record to the view under the name the
        // dashboard blade expects, so the Status card/Profile tab can show the
        // real active/warning/blacklisted state instead of a hardcoded value.
        $currentStudent = $student;

        // 2. ✨ SYNCHRONIZED LOGIC: Get ALL active topics to match the lecturer matrix view perfectly
        $allTopics = Topic::all();

        $totalParticipationScore = 0;
        $maxPossibleMarks = $allTopics->count() * 20; // 20 marks max per available topic

        foreach ($allTopics as $topic) {
            $replyCount = Message::where('topic_id', $topic->id)
                ->where('user_id', $userId)
                ->count();

            $calculatedScore = $replyCount * 2;
            if ($calculatedScore > 20) {
                $calculatedScore = 20;
            }

            $totalParticipationScore += $calculatedScore;
        }

        // 3. Quizzes currently within their live time window for this student's course
        $activeQuizzes = Quiz::when($studentCourse, function ($query, $course) {
                return $query->where('courseCode', $course);
            })
            ->where('startTime', '<=', now())
            ->where('expiryTime', '>=', now())
            ->get();

        // 4. Which of those the student has already submitted (by regNo, not user_id)
        $submittedQuizIDs = collect();
        if ($studentRegNo) {
            $submittedQuizIDs = DB::table('quiz_submissions')
                ->where('regNo', $studentRegNo)
                ->whereIn('quizID', $activeQuizzes->pluck('quizID'))
                ->pluck('quizID');
        }

        $completedQuizzes = $activeQuizzes->filter(function ($quiz) use ($submittedQuizIDs) {
            return $submittedQuizIDs->contains($quiz->quizID);
        })->values();

        // 5. 🔒 LOCKDOWN: the first currently-live quiz this student has NOT submitted yet.
        //    No separate "status" or "attempts" table needed — absence of a quiz_submissions
        //    row for this regNo + quizID IS "in progress" under this schema.
        $activeQuiz = $activeQuizzes->first(function ($quiz) use ($submittedQuizIDs) {
            return !$submittedQuizIDs->contains($quiz->quizID);
        });

        // 6. Handle Tab State (Defaults to 'dashboard', switches to 'announcements' if requested)
        $currentTab = $request->query('tab', 'dashboard');
        $announcements = Announcement::latest()->get();

        return view('dashboards.student', compact(
            'activeQuizzes',
            'completedQuizzes',
            'activeQuiz',
            'totalParticipationScore',
            'maxPossibleMarks',
            'currentTab',
            'announcements',
            'currentStudent' // ➕ ADDED
        ));
    }

    /**
     * 🔄 JSON status check, polled from the dashboard so a newly-live quiz
     * locks the student down without requiring a manual page refresh.
     */
    public function lockStatus(Request $request)
    {
        $userId = Auth::id();
        $student = Student::where('user_id', $userId)->first();

        if (!$student || !$student->courseCode || !$student->regNo) {
            return response()->json(['locked' => false]);
        }

        $liveQuizzes = Quiz::where('courseCode', $student->courseCode)
            ->where('startTime', '<=', now())
            ->where('expiryTime', '>=', now())
            ->get();

        if ($liveQuizzes->isEmpty()) {
            return response()->json(['locked' => false]);
        }

        $submittedQuizIDs = \Illuminate\Support\Facades\DB::table('quiz_submissions')
            ->where('regNo', $student->regNo)
            ->whereIn('quizID', $liveQuizzes->pluck('quizID'))
            ->pluck('quizID');

        $activeQuiz = $liveQuizzes->first(function ($quiz) use ($submittedQuizIDs) {
            return !$submittedQuizIDs->contains($quiz->quizID);
        });

        if (!$activeQuiz) {
            return response()->json(['locked' => false]);
        }

        return response()->json([
            'locked' => true,
            'redirect_url' => route('quizzes.show', ['quizID' => $activeQuiz->quizID]),
        ]);
    }

    /**
     * DEACTIVATED: Automated scoring active.
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('error', 'Manual grading is disabled. Scores are managed automatically by the forum engine.');
    }
}
