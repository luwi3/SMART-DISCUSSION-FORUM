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
        $topics = Topic::orderBy('created_at', 'asc')->get();

        $students = User::where('role', 'student')
                        ->orderBy('name', 'asc')
                        ->get();

        $matrix = [];

        foreach ($students as $student) {
            foreach ($topics as $topic) {
                $messageCount = Message::where('topic_id', $topic->id)
                                       ->where('user_id', $student->id)
                                       ->count();

                $calculatedScore = $messageCount * 2;
                if ($calculatedScore > 20) {
                    $calculatedScore = 20;
                }

                $matrix[$student->id][$topic->id] = $calculatedScore;

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

        $student = Student::where('user_id', $userId)->first();

        // 🔧 FIX: normalize here, once, right after loading the student — everything
        // below (both the query and the collection filters) now uses this same
        // $studentCourse value instead of the raw, unnormalized column.
        $studentCourse = $student && $student->courseCode
            ? trim(strtoupper($student->courseCode))
            : null;
        $studentRegNo  = $student ? $student->regNo : null;

        $currentStudent = $student;

        $allTopics = Topic::all();

        $totalParticipationScore = 0;
        $maxPossibleMarks = $allTopics->count() * 20;

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
        // 🔧 FIX: this was Quiz::where('courseCode', $course) — an exact match against
        // the raw Student::courseCode. That's the bug behind the "No active evaluation
        // windows" message you saw even while a quiz was inside its start/expiry window:
        // the quiz's courseCode (uppercased on save) simply never matched the student's
        // as-typed courseCode. TRIM/UPPER both sides so this lines up with the middleware
        // and lockStatus() below.
        $activeQuizzes = Quiz::when($studentCourse, function ($query, $course) {
                return $query->whereRaw('TRIM(UPPER(courseCode)) = ?', [$course]);
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
        $activeQuiz = $activeQuizzes->first(function ($quiz) use ($submittedQuizIDs) {
            return !$submittedQuizIDs->contains($quiz->quizID);
        });

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
            'currentStudent'
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

        // 🔧 FIX: same TRIM/UPPER normalization as the other two — this endpoint
        // is what your 20s poll on the dashboard hits, so before this fix a quiz
        // going live mid-session would silently never trigger the redirect either.
        $studentCourse = trim(strtoupper($student->courseCode));

        $liveQuizzes = Quiz::whereRaw('TRIM(UPPER(courseCode)) = ?', [$studentCourse])
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