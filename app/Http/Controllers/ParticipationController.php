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

class ParticipationController extends Controller
{
    /**
     * 👨‍🏫 Display the dynamic, system-automated Topic Participation Grade Matrix for Lecturers.
     */
   public function index(Request $request)
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
                ['topic_id' => $topic->id, 'user_id' => $student->id],
                ['marks_earned' => $calculatedScore]
            );
        }
    }

    if ($request->wantsJson()) {
        return response()->json(compact('topics', 'students', 'matrix'));
    }

    return view('participation.index', compact('topics', 'students', 'matrix'));
}
    /**
     * 🎓 Render Student Dashboard with synchronized Forum Participation Marks & Dynamic Tabs
     */
    public function studentDashboard(Request $request)
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
        $currentTab = $request->query('tab', 'main');
        $data=compact(
            'activeQuizzes', 
            'completedQuizzes',
            'totalParticipationScore', 
            'maxPossibleMarks',
            'currentTab'
            
        );
 if ($request->wantsJson()) {
        return response()->json($data);
    }

        return view('dashboards.student',$data);
    }

    /**
     * DEACTIVATED: Automated scoring active.
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('error', 'Manual grading is disabled. Scores are managed automatically by the forum engine.');
    }
}