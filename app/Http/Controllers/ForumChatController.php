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
use App\Services\TopicService;
use App\Jobs\ProcessMessageAI;
use App\Services\SpamDetectionService;

class ForumChatController extends Controller
{
    protected $spamDetectionService;
    protected $topicService;

    public function __construct(
        SpamDetectionService $spamDetectionService,
        TopicService $topicService
    ) {
        $this->spamDetectionService = $spamDetectionService;
        $this->topicService = $topicService;
    }

    public function index(Request $request, $type = null, $id = null)
    {
        // 🎯 CHECK: Incoming request arrived from a notification via query parameter (?topic=ID)
        if ($request->has('topic')) {
            $type = 'topic';
            $id = $request->query('topic');
        }

        $userId = auth()->id();

        // 🔍 Dynamic structural detection to find what column your group relationship uses
        $groupColumn = null;
        foreach (['group_discussion_id', 'group_id', 'discussion_id'] as $column) {
            if (Schema::hasColumn('messages', $column)) {
                $groupColumn = $column;
                break;
            }
        }

        // =================================================================
        // 🌟 STEP 1: RESET UNREAD COUNT UPON ENTERING A CHAT ROOM
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
                $lastView = DB::table('chat_views')
                    ->where('user_id', $userId)
                    ->where('chat_type', 'group')
                    ->where('chat_id', $group->id)
                    ->value('last_read_at');

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
        // CHAT MESSAGE STREAM RESOLUTION
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
        }

        $currentStudent = Student::where('user_id', auth()->id())->first();

        $courses = Student::select('courseCode')
            ->distinct()
            ->whereNotNull('courseCode')
            ->get();

        if (!$type || $type === 'broadcast') {
            $messages = Message::where(function ($q) use ($currentStudent) {
                $q->whereNull('course_code');

                if ($currentStudent) {
                    $q->orWhere('course_code', $currentStudent->courseCode);
                }
            })
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
        }

        // Desktop App / JSON API Request Support
        if ($request->wantsJson()) {
            return response()->json(compact('topics', 'sidebarGroups', 'messages', 'type', 'id', 'mainChatUnread', 'courses'));
        }

        return view('chat.index', compact(
            'topics',
            'sidebarGroups',
            'currentStreamTarget',
            'messages',
            'type',
            'id',
            'currentStudent',
            'mainChatUnread',
            'courses'
        ));
    }

    public function store(Request $request, $type = null, $id = null)
    {
        $request->validate([
            'body' => 'required|string|max:3000'
        ]);

        // Fall back to request payload if route parameters are missing
        $type = $type ?? $request->input('type');
        $id = $id ?? $request->input('id');

        $student = Student::where('user_id', auth()->id())->first();
        
        // Restricted course verification
        if ($request->filled('restrict_course_id')) {
            if (!$student || $student->courseCode != $request->restrict_course_id) {
                return response()->json([
                    'error' => 'You cannot send a message restricted to this course.'
                ], 422);
            }
        }

        // 🛑 Blacklist Enforcement Check
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

        $message = new Message();
        $message->user_id = auth()->id();
        $message->body = $request->body;

        /*
        |--------------------------------------------------------------------------
        | Spam Detection
        |--------------------------------------------------------------------------
        */
        $isSpam = $this->spamDetectionService->checkSpam($message->body);

        if ($isSpam) {
            return response()->json([
                'spam' => true,
                'message' => 'This message was deleted because it was detected as spam.'
            ], 422);
        }

        if ($request->filled('restrict_course_id')) {
            $message->course_code = $request->restrict_course_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Reply and Thread Handling
        |--------------------------------------------------------------------------
        */
        if ($request->filled('reply_to_message_id')) {
            $parentMessage = Message::find($request->reply_to_message_id);

            if ($parentMessage) {
                if ($parentMessage->course_code) {
                    $message->course_code = $parentMessage->course_code;
                }

                $message->reply_to_message_id = $parentMessage->id;
                $message->thread_id = $parentMessage->thread_id ?? $parentMessage->id;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Detect Group Column
        |--------------------------------------------------------------------------
        */
        $groupColumn = null;
        foreach (['group_discussion_id', 'group_id', 'discussion_id'] as $column) {
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
            && $id !== 'general'
            && Schema::hasColumn('messages', 'topic_id')
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
        | AI Topic Processing
        |--------------------------------------------------------------------------
        */
        $isGeneralChat = (!$type || $type === 'broadcast' || $id === 'general');
        $isNewDiscussion = ($message->thread_id === null);
        $isNotAlreadyTopic = ($message->topic_id === null);
        $isNotGroupChat = !($type === 'group' && $id !== 'general');

        if (
            $isGeneralChat &&
            $isNewDiscussion &&
            $isNotAlreadyTopic &&
            $isNotGroupChat
        ) {
            ProcessMessageAI::dispatch($message->id);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Read Status
        |--------------------------------------------------------------------------
        */
        if ($type === 'group' && $id !== 'general') {
            DB::table('chat_views')->updateOrInsert(
                ['user_id' => auth()->id(), 'chat_type' => 'group', 'chat_id' => $id],
                ['last_read_at' => now(), 'updated_at' => now()]
            );
        } elseif ($type === 'topic' && $id !== 'general') {
            DB::table('chat_views')->updateOrInsert(
                ['user_id' => auth()->id(), 'chat_type' => 'topic', 'chat_id' => $id],
                ['last_read_at' => now(), 'updated_at' => now()]
            );
        } else {
            DB::table('chat_views')->updateOrInsert(
                ['user_id' => auth()->id(), 'chat_type' => 'main', 'chat_id' => 0],
                ['last_read_at' => now(), 'updated_at' => now()]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Student Activity Update
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
        | Broadcast & Response
        |--------------------------------------------------------------------------
        */
        $message->load('user');
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        // Return Desktop App / API JSON format
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'success' => true,
                'message' => $message
            ]);
        }

        if ($type === 'topic' && auth()->user()->role === 'student') {
            return back()->with(
                'success',
                'Your reply has been posted! Live participation marks have synced.'
            );
        }

        return back();
    }

    /**
     * Delete a chat message.
     */
    public function destroy(Message $message)
    {
        $currentUser = auth()->user();

        // 🔒 Authorization Check: User owns the message OR is an admin/lecturer
        $isOwner = $currentUser->id === $message->user_id;
        $isModerator = in_array($currentUser->role ?? '', ['admin', 'administrator', 'lecturer']);

        if (!$isOwner && !$isModerator) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'You are not authorized to delete this message.'], 403);
            }
            abort(403, 'You are not authorized to delete this message.');
        }

        $message->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Message deleted.']);
        }

        return back()->with('success', 'Message deleted successfully.');
    }
}