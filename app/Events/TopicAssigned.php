<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicAssigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $message;



    public function __construct(Message $message)
    {
        $this->message = $message->load([
            'user',
            'topic'
        ]);
    }



    public function broadcastOn()
    {
        // Tell users in the main chat
        // that this message moved to a topic

        return new Channel('chat.broadcast.general');
    }



    public function broadcastWith(): array
    {
        return [

            'message' => [

                'id' => $this->message->id,

                'body' => $this->message->body,

                'topic_id' => $this->message->topic_id,


                'topic' => [

                    'id' => $this->message->topic->id,

                    'title' => $this->message->topic->title,

                    'description' => $this->message->topic->description

                ],


                'user' => [

                    'name' => $this->message->user->name ?? 'User'

                ]

            ]

        ];
    }



    public function broadcastAs()
    {
        return 'TopicAssigned';
    }
}