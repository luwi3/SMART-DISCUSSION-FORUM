<?php

namespace App\Http\Controllers;

use App\Models\GroupDiscussion;
use App\Models\Topic;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\NewTopicNotification;
use App\Models\Student;

class ForumChatController extends Controller
{
    public function index(Request $request, $type = null, $id = null)
    {
        // 🎯 CHECK: Incoming request arrived from a notification via query parameter (?topic=ID)
        if ($request->has('topic')) {
            $type = 'topic';
            $id = $request->query('topic');
        }

        $userId = auth()->id();

        // Dynamic structural detection for group column
        $groupColumn = null;
        foreach (['group_discussion_id', 'group_id', 'discussion_id'] as $column) {
            if (Schema::hasColumn('messages', $column)) {
                $groupColumn = $column;
                break;
            }
        }

        // =================================================================
        // 🌟 STEP 5: RESET UNREAD COUNT UPON ENTERING A CHAT ROOM
        // =================================================================
        if (auth()->check()) {
            if ($type === 'group' && $id && $id !== 'general') {
                DB::table('chat_views')->updateOrInsert(
                    ['user_id' => $userId, 'chat_type' => 'group', 'chat_id' => $id],
                    ['last_read_at' => now(), 'updated_at' => now()]
                );
            } elseif ($type === 'topic' && $id && $id !== 'general') {
                DB::table('chat_views')->updateOrInsert(
                    ['user_id' => $userId, 'chat_type' => 'topic', 'chat_id' => $id],
                    ['last_read_at' => now(), 'updated_at' => now()]
                );
            } elseif (!$type || $type === 'broadcast' || $id === 'general') {
                // Main / General Broadcast view
                DB::table('chat_views')->updateOrInsert(
                    ['user_id' => $userId, 'chat_type' => 'main', 'chat_id' => 0],
                    ['last_read_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // =================================================================
        // 🌟 STEP 2: COUNT UNREAD MESSAGES FOR THE SIDEBAR
        // =================================================================
        
        // 1. Fetch Main Chat Broadcast Count
        $mainChatLastView = DB::table('chat_views')
            ->where('user_id', $userId)
            ->where('chat_type', 'main')
            ->where('chat_id', 0)
            ->value('last_read_at');

        $mainChatQuery = Message::query()->where('user_id', '!=', $userId);
        if ($groupColumn) { $mainChatQuery->whereNull($groupColumn); }
        if (Schema::hasColumn('messages', 'topic_id')) { $mainChatQuery->whereNull('topic_id'); }
        
        $mainChatUnread = $mainChatQuery->when($mainChatLastView, function ($query) use ($mainChatLastView) {
            return $query->where('created_at', '>', $mainChatLastView);
        })->count();

        // 2. Fetch Topics with dynamic unread calculation
        $topics = Topic::orderBy('title', 'asc')->get()->map(function ($topic) use ($userId) {
            $lastView = DB::table('chat_views')
                ->where('user_id', $userId)
                ->where('chat_type', 'topic')
                ->where('chat_id', $topic->id)
                ->value('last_read_at');

            $topic->unread_count = Message::where('topic_id', $topic->id)
                ->where('user_id', '!=', $userId)
                ->when($lastView, function ($query) use ($lastView) {
                    return $query->where('created_at', '>', $lastView);
                })->count();

            return $topic;
        }); 
        
        // 3. Fetch Sidebar Groups with robust dynamic unread calculation & safety cleanups
        $sidebarGroups = collect();
        if (auth()->check()) {
            $rawGroups = auth()->user()->groups ?? collect(); 
            $sidebarGroups = $rawGroups->map(function ($group) use ($userId, $groupColumn) {
                // Fetch when this specific user last looked at this group room
                $lastView = DB::table('chat_views')
                    ->where('user_id', $userId)
                    ->where('chat_type', 'group')
                    ->where('chat_id', $group->id)
                    ->value('last_read_at');

                // Determine target query column using detected configuration or fallbacks
                $targetColumn = $groupColumn;
                if (!$targetColumn) {
                    if (Schema::hasColumn('messages', 'group_discussion_id')) { $targetColumn = 'group_discussion_id'; }
                    elseif (Schema::hasColumn('messages', 'group_id')) { $targetColumn = 'group_id'; }
                    elseif (Schema::hasColumn('messages', 'discussion_id')) { $targetColumn = 'discussion_id'; }
                }

                if ($targetColumn) {
                    $group->unread_count = Message::where($targetColumn, $group->id)
                        ->where('user_id', '!=', $userId)
                        ->when($lastView, function ($query) use ($lastView) {
                            return $query->where('created_at', '>', $lastView);
                        })->count();
                } else {
                    $group->unread_count = 0;
                }

                return $group;
            });
        }
        
        // =================================================================
        // CHAT MESSAGE STREAM RESOLUTION (Original Logic Preserved)
        // =================================================================
        $messages = collect();
        $currentStreamTarget = null;

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
        // Pass all data down cleanly to the Blade template
        return view('chat.index', compact('topics', 'sidebarGroups', 'currentStreamTarget', 'messages', 'type', 'id', 'currentStudent', 'mainChatUnread'));
    }

    public function store(Request $request, $type = null, $id = null)
    {
        $request->validate([
            'body' => 'required|string|max:3000'
        ]);

        // 🔧 FIX: Fall back to request payload if route parameters are missing
        $type = $type ?? $request->input('type');
        $id = $id ?? $request->input('id');

        $student = Student::where('user_id', auth()->id())->first();

        if ($student && $student->status === 'blacklisted') {

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'errors' => [
                        'message' => [
                            'You have been blocked from using the chat due to inactivity until further notice.'
                        ]
                    ]
                ], 422);
            }

            return back()->withErrors([
                'message' => 'You have been blocked from using the chat due to inactivity until further notice.'
            ]);
        }

        // 4. Create and save new message
        $message = new Message();

        $message->user_id = auth()->id();
        $message->body = $request->body;

        /*
        |--------------------------------------------------------------------------
        | Reply and Thread Handling
        |--------------------------------------------------------------------------
        */

        if ($request->filled('reply_to_message_id')) {

            $parentMessage = Message::find($request->reply_to_message_id);

            if ($parentMessage) {

                // The exact message being replied to
                $message->reply_to_message_id = $parentMessage->id;

                // Keep replies inside the same thread
                if ($parentMessage->thread_id) {
                    $message->thread_id = $parentMessage->thread_id;
                } else {
                    $message->thread_id = $parentMessage->id;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Detect Group Column
        |--------------------------------------------------------------------------
        */

        $groupColumn = null;

        foreach ([
            'group_discussion_id',
            'group_id',
            'discussion_id'
        ] as $column) {

            if (Schema::hasColumn('messages', $column)) {
                $groupColumn = $column;
                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Attach Message Location
        |--------------------------------------------------------------------------
        */

        if ($type === 'group' && $id !== 'general' && $groupColumn) {
            $message->{$groupColumn} = $id;
        }

        if (
            $type === 'topic'
            && 
            $id !== 'general'
            &&
            Schema::hasColumn('messages', 'topic_id')
        ) {
            $message->topic_id = $id;
        }

        /*
        |--------------------------------------------------------------------------
        | Save Message
        |--------------------------------------------------------------------------
        */

        $message->save();
        
        



        /*
        |--------------------------------------------------------------------------
        | Update Read Status
        |--------------------------------------------------------------------------
        */

        if ($type === 'group' && $id !== 'general') {

            DB::table('chat_views')->updateOrInsert(
                [
                    'user_id' => auth()->id(),
                    'chat_type' => 'group',
                    'chat_id' => $id
                ],
                [
                    'last_read_at' => now(),
                    'updated_at' => now()
                ]
            );

        } elseif ($type === 'topic' && $id !== 'general') {

            DB::table('chat_views')->updateOrInsert(
                [
                    'user_id' => auth()->id(),
                    'chat_type' => 'topic',
                    'chat_id' => $id
                ],
                [
                    'last_read_at' => now(),
                    'updated_at' => now()
                ]
            );

        } else {

            DB::table('chat_views')->updateOrInsert(
                [
                    'user_id' => auth()->id(),
                    'chat_type' => 'main',
                    'chat_id' => 0
                ],
                [
                    'last_read_at' => now(),
                    'updated_at' => now()
                ]
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Student Activity
        |--------------------------------------------------------------------------
        */

        if ($student) {

            $student->lastCommDate = now();

            if ($student->status === 'warning') {
                $student->status = 'active';
            }

            $student->save();
        }

        /*
        |--------------------------------------------------------------------------
        | Broadcast
        |--------------------------------------------------------------------------
        */

        $message->load('user');
        broadcast(new \App\Events\MessageSent($message));


        // 🔧 FIX: Support both standard form redirects and JSON responses for JavaScript fetch() calls
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        if (
            $type === 'topic'
            &&
            auth()->user()->role === 'student'
        ) {

            return back()->with(
                'success',
                'Your reply has been posted! Live participation marks have synced.'
            );

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
}