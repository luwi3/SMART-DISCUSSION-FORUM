<?php

namespace App\Http\Controllers;

use App\Models\GroupDiscussion;
use App\Models\Topic;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Student;

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

        // 🔍 Dynamic structural detection to find what column your group relationship uses
        $groupColumn = null;
        foreach (['group_discussion_id', 'group_id', 'discussion_id'] as $column) {
            if (Schema::hasColumn('messages', $column)) {
                $groupColumn = $column;
                break;
            }
        }

        // Check if a specific group or topic is being viewed (Ignore fallback 'general' ID)
        if ($type && $id && $id !== 'general' && $type !== 'broadcast') {
            if ($type === 'group') {
                $currentStreamTarget = GroupDiscussion::findOrFail($id);
                if ($groupColumn) {
                    $messages = Message::where($groupColumn, $id)->with('user')->orderBy('created_at', 'asc')->get();
                }
            } elseif ($type === 'topic') {
                $currentStreamTarget = Topic::findOrFail($id);
                $messages = Message::where('topic_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
            }
        } else {
            // 🎯 FIX: Force target to true/object placeholder so the Blade view allows message iteration
            $currentStreamTarget = (object) ['name' => 'General Stream', 'is_broadcast' => true];

            // Load global broadcast messages safely without crashing on missing group columns
            $query = Message::query();
            
            if ($groupColumn) {
                $query->whereNull($groupColumn);
            }
            if (Schema::hasColumn('messages', 'topic_id')) {
                $query->whereNull('topic_id');
            }
            
            $messages = $query->with('user')->orderBy('created_at', 'asc')->get();
        }

        // Pass all variables, including the new $topics, to the Blade view
        $currentStudent = \App\Models\Student::where('user_id', auth()->id())->first();

        return view('chat.index', compact('groups', 'topics', 'currentStreamTarget', 'messages', 'type', 'id', 'currentStudent'));
    }

    public function store(Request $request, $type = null, $id = null)
    {
        // 1. Validate the incoming message
        $request->validate(['body' => 'required|string|max:3000']);

        // 2. Fetch the student record for the logged-in user
        $student = Student::where('user_id', auth()->id())->first();

        // 3. Check if they are blacklisted
        if ($student && $student->status === 'blacklisted') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'errors' => ['message' => ['You have been blocked from using the chat due to inactivity until further notice.']]
                ], 422);
            }

            return back()->withErrors(['message' => 'You have been blocked from using the chat due to inactivity until further notice.']);
        }

        $message = new Message();
        $message->user_id = auth()->id();
        $message->body = $request->body;

        // 🔍 Dynamically detect column name before saving target ID
        $groupColumn = null;
        foreach (['group_discussion_id', 'group_id', 'discussion_id'] as $column) {
            if (Schema::hasColumn('messages', $column)) {
                $groupColumn = $column;
                break;
            }
        }

        if ($type === 'group' && $id !== 'general' && $groupColumn) {
            $message->{$groupColumn} = $id;
        }
        if ($type === 'topic' && $id !== 'general' && Schema::hasColumn('messages', 'topic_id')) {
            $message->topic_id = $id;
        }

        $message->save();

        // ⏱️ Update communication timestamp and reset warnings
        if ($student) {
            $student->lastCommDate = now();

            // 🔄 Automatically bring warned students back to active
            if ($student->status === 'warning') {
                $student->status = 'active';
            }

            $student->save();
        }

        $message->load('user'); 

        broadcast(new \App\Events\MessageSent($message))->toOthers();
        
        // 🌟 UX Optimization: Send an encouraging confirmation if a student participates in a graded topic
        if ($type === 'topic' && auth()->user()->role === 'student') {
            return back()->with('success', 'Your reply has been posted! Live participation marks have synced.');
        }

        return back();
    }
}