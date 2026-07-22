<?php

namespace App\Http\Controllers;

use App\Models\GroupDiscussion;
use App\Models\Topic;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Notifications\NewTopicNotification;
use App\Models\Student;

class ForumChatController extends Controller
{
    public function index(Request $request, $type = null, $id = null)
    {
        // Check if incoming request arrived from a notification via query parameter (?topic=ID)
        if ($request->has('topic')) {
            $type = 'topic';
            $id = $request->query('topic');
        }

        // Fetch all topics for the sidebar
        $topics = Topic::orderBy('title', 'asc')->get(); 
        
        $sidebarGroups = collect();
        if (auth()->check()) {
            $sidebarGroups = auth()->user()->groups ?? collect(); 
        }
        
        $messages = collect();
        $currentStreamTarget = null;

        // Dynamic structural detection for group column
        $groupColumn = null;
        foreach (['group_discussion_id', 'group_id', 'discussion_id'] as $column) {
            if (Schema::hasColumn('messages', $column)) {
                $groupColumn = $column;
                break;
            }
        }

        if ($type && $id && $id !== 'general' && $type !== 'broadcast') {
            if ($type === 'group') {
                if (class_exists(\App\Models\GroupDiscussion::class)) {
                    $currentStreamTarget = GroupDiscussion::findOrFail($id);
                } else {
                    $currentStreamTarget = \App\Models\Group::findOrFail($id);
                }

                if ($groupColumn) {
                    $messages = Message::where($groupColumn, $id)->with('user')->orderBy('created_at', 'asc')->get();
                }
            } elseif ($type === 'topic') {
                $currentStreamTarget = Topic::findOrFail($id);
                $messages = Message::where('topic_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
            }
        } else {
            $currentStreamTarget = (object) ['name' => 'General Stream', 'is_broadcast' => true];

            $query = Message::query();
            
            if ($groupColumn) {
                $query->whereNull($groupColumn);
            }
            if (Schema::hasColumn('messages', 'topic_id')) {
                $query->whereNull('topic_id');
            }
            
            $messages = $query->with('user')->orderBy('created_at', 'asc')->get();
        }

        $currentStudent = Student::where('user_id', auth()->id())->first();

        // JSON response for API / Java desktop client
        if ($request->wantsJson()) {
            return response()->json(compact('topics', 'sidebarGroups', 'messages', 'type', 'id'));
        }

        return view('chat.index', compact('topics', 'sidebarGroups', 'currentStreamTarget', 'messages', 'type', 'id', 'currentStudent'));
    }

    public function store(Request $request, $type = null, $id = null)
    {
        // 1. Validate incoming message
        $request->validate(['body' => 'required|string|max:3000']);

        // 2. Fetch student record
        $student = Student::where('user_id', auth()->id())->first();

        // 3. Check if blacklisted 🛑
        if ($student && $student->status === 'blacklisted') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'errors' => ['message' => ['You have been blocked from using the chat due to inactivity until further notice.']]
                ], 422);
            }

            return back()->withErrors(['message' => 'You have been blocked from using the chat due to inactivity until further notice.']);
        }

        // 4. Create and save new message
        $message = new Message();
        $message->user_id = auth()->id();
        $message->body = $request->body;

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

        // 5. Update activity status
        if ($student) {
            $student->lastCommDate = now();

            if ($student->status === 'warning') {
                $student->status = 'active';
            }

            $student->save();
        }

        $message->load('user'); 

        broadcast(new \App\Events\MessageSent($message))->toOthers();
        
        // Response for Java API client
        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }

        if ($type === 'topic' && auth()->user()->role === 'student') {
            return back()->with('success', 'Your reply has been posted! Live participation marks have synced.');
        }

        return back();
    }
}