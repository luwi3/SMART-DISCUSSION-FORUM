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
        $groups = GroupDiscussion::orderBy('name', 'asc')->get();
        $messages = collect();
        $currentStreamTarget = null;

        if ($type && $id) {
            if ($type === 'group') {
                $currentStreamTarget = GroupDiscussion::findOrFail($id);
                $messages = Message::where('group_discussion_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
            } elseif ($type === 'topic') {
                $currentStreamTarget = Topic::findOrFail($id);
                $messages = Message::where('topic_id', $id)->with('user')->orderBy('created_at', 'asc')->get();
            }
        }

        return view('chat.index', compact('groups', 'currentStreamTarget', 'messages', 'type', 'id'));
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
        $message->load('user'); // Essential for broadcasting the user name

        broadcast(new \App\Events\MessageSent($message))->toOthers();
        return back();
    }
}