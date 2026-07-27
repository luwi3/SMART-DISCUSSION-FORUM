<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // Same channel a live message from this thread would have used —
    // see MessageSent::broadcastOn(), kept in sync deliberately.
    public function broadcastOn()
    {
        if ($this->message->topic_id) {
            return new Channel('chat.topic.' . $this->message->topic_id);
        }

        $groupColumns = ['group_discussion_id', 'group_id', 'discussion_id'];

        foreach ($groupColumns as $column) {
            if (isset($this->message->{$column}) && $this->message->{$column}) {
                return new Channel('chat.group.' . $this->message->{$column});
            }
        }

        if ($this->message->course_code) {
            return new PrivateChannel('chat.course.' . $this->message->course_code);
        }

        return new Channel('chat.broadcast.general');
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'user_id' => $this->message->user_id,
                'deleted_by' => $this->message->deleted_by,
            ],
        ];
    }

    public function broadcastAs()
    {
        return 'MessageDeleted';
    }
}
