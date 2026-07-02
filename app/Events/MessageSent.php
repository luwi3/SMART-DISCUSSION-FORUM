<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        $type = $this->message->group_discussion_id ? 'group' : 'topic';
        $id = $this->message->group_discussion_id ?? $this->message->topic_id;
        return new Channel('chat.' . $type . '.' . $id);
    }

    // This ensures the JS receives the user data in the JSON payload
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'body' => $this->message->body,
                'user' => [
                    'name' => $this->message->user->name
                ]
            ]
        ];
    }
}