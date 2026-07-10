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
        // This guarantees that administrators and lecturers are completely excluded from the ledger view.
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
     * 🎓 Render Student Dashboard with synchronized Forum Participation Marks
     */
    public function studentDashboard()
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect('/login')->with('error', 'Please log in to view your dashboard.');
        }

        // 1. Get the student's profile records to find their course code string
        $student = Student::where('user_id', $userId)->first();
        $studentCourse = $student ? $student->courseCode : null;

        // 2. ✨ SYNCHRONIZED LOGIC: Get ALL active topics to match the lecturer matrix view perfectly
        $allTopics = Topic::all();
        
        $totalParticipationScore = 0;
        $maxPossibleMarks = $allTopics->count() * 20; // 20 marks max per available topic

        foreach ($allTopics as $topic) {
            // Count the number of replies this specific student created for this topic
            $replyCount = Message::where('topic_id', $topic->id)
                ->where('user_id', $userId)
                ->count();

            // Compute score: 2 marks per message, capped at 20 max
            $calculatedScore = $replyCount * 2; 
            if ($calculatedScore > 20) {
                $calculatedScore = 20;
            }

            $totalParticipationScore += $calculatedScore;
        }

        // 3. Keep active quizzes display decoupled
        $activeQuizzes = Quiz::when($studentCourse, function ($query, $course) {
                return $query->where('courseCode', $course);
            })
            ->where('startTime', '<=', now())
            ->where('expiryTime', '>=', now())
            ->get();

        $completedQuizzes = collect(); 

        return view('dashboards.student', compact(
            'activeQuizzes', 
            'completedQuizzes',
            'totalParticipationScore', 
            'maxPossibleMarks'
        ));
    }

    /**
     * DEACTIVATED: Automated scoring active.
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('error', 'Manual grading is disabled. Scores are managed automatically by the forum engine.');
    }
}