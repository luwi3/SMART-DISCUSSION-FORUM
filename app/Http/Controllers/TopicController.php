<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\TopicParticipation;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    /**
     * Show the creation form to the lecturer.
     */
    public function create()
    {
        return view('topics.create');
    }

    /**
     * Store a new topic created by a lecturer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Topic::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::id(), // Tracks which lecturer made it
        ]);

        return redirect()->route('chat.index')->with('success', 'Discussion topic created successfully!');
    }

    /**
     * Handle a student posting a response and award automatic marks.
     */
    public function reply(Request $request, $topicId)
    {
        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        $user = Auth::user();

        // 1. Save the post/reply to your comments or messages table here
        // (Assuming you have a Message or Reply model linked to topics)

        // 2. AUTOMATIC GRADING UTILITY: 
        // If it's a student, check if they already have an entry. If not, award base participation marks!
        if ($user->role === 'student') {
            
            // Look up if this student already participated in this specific topic
            $participation = TopicParticipation::where('topic_id', $topicId)
                                               ->where('user_id', $user->id)
                                               ->first();

            // Set up how many points a student gets automatically per discussion input thread
            $pointsToAward = $participation ? ($participation->marks_earned + 10) : 10; 

            // Cap the automatic score at a maximum limit of 100
            if ($pointsToAward > 100) {
                $pointsToAward = 100;
            }

            // Sync directly to the participation records matrix map
            TopicParticipation::updateOrCreate(
                [
                    'topic_id' => $topicId,
                    'user_id'  => $user->id,
                ],
                [
                    'marks_earned' => $pointsToAward
                ]
            );
        }

        return redirect()->back()->with('success', 'Reply posted! Your participation score has updated.');
    }
}