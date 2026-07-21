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
        // 1. Validate 'title' and 'description'
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        // 2. Map form data and create the topic record
        $topic = Topic::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id()
        ]);

        // 3. 🔔 NOTIFICATION SYSTEM: Get all users except the creator
        $usersToNotify = User::where('id', '!=', auth()->id())->get();
        
        // Send the notification via database + Reverb broadcast pipeline
        Notification::send($usersToNotify, new NewTopicNotification($topic));
        return response()->json([
    'success' => true,
    'message' => 'Topic created successfully!',
    'topic' => $topic
], 201);

        // 4. Redirect back to the switchboard dashboard
        return redirect()->route('dashboard')->with('success', 'Topic created successfully!');
    }
}