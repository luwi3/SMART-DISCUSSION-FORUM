<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load([
            'user',
            'replyTo',
        ]);
    }

    public function broadcastOn()
    {
        // 1. Check if the message belongs to a course topic
        if ($this->message->topic_id) {
            return new Channel('chat.topic.' . $this->message->topic_id);
        }

        // 2. Check for any of your potential group column names
        $groupColumns = ['group_discussion_id', 'group_id', 'discussion_id'];

        foreach ($groupColumns as $column) {
            if (isset($this->message->{$column}) && $this->message->{$column}) {
                return new Channel('chat.group.' . $this->message->{$column});
            }
        }

        // 3. Fallback to the global main chat broadcast channel
        return new Channel('chat.broadcast.general');
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'topic_id' => $this->message->topic_id,
                'user' => [
                    'name' => $this->message->user->name ?? 'Peer User',
                ],
                'user_id' => $this->message->user_id,
                'reply_to_message_id' => $this->message->reply_to_message_id,
                'created_at' => $this->message->created_at->toISOString(),
            ],
        ];
    }

    // Explicitly define the event name so it perfectly matches your JS
    public function broadcastAs()
    {
        return 'MessageSent';
    }
}