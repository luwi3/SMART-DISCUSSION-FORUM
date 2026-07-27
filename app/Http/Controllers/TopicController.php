<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\User; // 👤 Imported to find users to notify
use App\Notifications\NewTopicNotification; // 🔔 Imported your notification class
use Illuminate\Support\Facades\Notification;

class TopicController extends Controller
{
    public function create()
    {
        return view('topics.create');
    }

    public function store(Request $request)
    {
        // 1. Validate 'title' and 'description' with strong input constraints
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // 2. Create and store the topic record in $topic for notifications
        $topic = Topic::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => auth()->id(),
        ]);

        // 3. 🔔 NOTIFICATION SYSTEM: Get all users except the creator
        $usersToNotify = User::where('id', '!=', auth()->id())->get();
        
        // Send the notification via database + Reverb broadcast pipeline
        Notification::send($usersToNotify, new NewTopicNotification($topic));

        // 4. Respond appropriately depending on how the request was made
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic created successfully!',
                'topic' => $topic
            ], 201);
        }

        // Normal browser form submission -> redirect back to the switchboard dashboard
        return redirect()->route('dashboard')->with('success', 'Topic created successfully!');
    }
}