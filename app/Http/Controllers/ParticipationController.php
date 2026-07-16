<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Topic;
use App\Models\TopicParticipation;
use App\Models\Message; // 👈 Targets your updated 'messages' table model directly

class ParticipationController extends Controller
{
    /**
     * Display the dynamic, system-automated Topic Participation Grade Matrix.
     */
    public function index()
    {
        // 1. Fetch all active topics ordered by creation date (Generates columns automatically)
        $topics = Topic::orderBy('created_at', 'asc')->get();

        // 2. Fetch all students ordered alphabetically by name (Generates rows)
        $students = User::where('role', 'student')->orderBy('name', 'asc')->get();

        // 3. Map student message metrics directly into a look-up grid matrix layout
        $matrix = [];
        
        foreach ($students as $student) {
            foreach ($topics as $topic) {
                // 📊 SYSTEM LOGIC: Count rows using your exact database keys
                $messageCount = Message::where('topic_id', $topic->id)
                                       ->where('user_id', $student->id)
                                       ->count();

                // 🧮 SYSTEM ALGORITHM: Each reply adds 5 points, capped at 20 marks total
                $calculatedScore = $messageCount * 5;
                if ($calculatedScore > 20) {
                    $calculatedScore = 20; 
                }

                // Assign calculated data to the array lookup map matching your sketch layout
                $matrix[$student->id][$topic->id] = $calculatedScore;

                // Sync data down to your persistent records storage table
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
     * DEACTIVATED: Automated scoring active.
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('error', 'Manual grading is disabled. Scores are managed automatically by the forum engine.');
    }
}