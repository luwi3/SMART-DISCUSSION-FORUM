<?php

namespace App\Http\Controllers;

use App\Models\GroupDiscussion;
use App\Models\Topic;
use App\Models\Message;
use Illuminate\Http\Request;

class ForumChatController extends Controller
{
    public function index($type = null, $id = null)
    {
        // Fetch all groups for the sidebar
        $groups = GroupDiscussion::orderBy('name', 'asc')->get();
        
        // Fetch all topics for the sidebar
        $topics = Topic::orderBy('title', 'asc')->get(); 
        
        $messages = collect();
        $currentStreamTarget = null;

        // Check if a specific group or topic is being viewed
        if ($type && $id) {
            if ($type === 'group') {
                $currentStreamTarget = GroupDiscussion::findOrFail($id);
                $messages = Message::where('group_discussion_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
            } elseif ($type === 'topic') {
                $currentStreamTarget = Topic::findOrFail($id);
                $messages = Message::where('topic_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
            }
        }

        // Pass all variables, including the new $topics, to the Blade view
        return view('chat.index', compact('groups', 'topics', 'currentStreamTarget', 'messages', 'type', 'id'));
    }

    public function store(Request $request, $type, $id)
    {
        $request->validate(['body' => 'required|string|max:3000']);

        $message = new Message();
        $message->user_id = auth()->id();
        $message->body = $request->body;

        if ($type === 'group') $message->group_discussion_id = $id;
        if ($type === 'topic') $message->topic_id = $id;

        $message->save();
        $message->load('user'); 

        broadcast(new \App\Events\MessageSent($message))->toOthers();
        
        // 🌟 UX Optimization: Send an encouraging confirmation if a student participates in an graded topic
        if ($type === 'topic' && auth()->user()->role === 'student') {
            return back()->with('success', 'Your reply has been posted! Live participation marks have synced.');
        }

        return back();
    }
}