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
    return new Channel(
        'chat.topic.' . $this->message->topic_id
    );
}



   public function broadcastWith(): array
{
    return [
        'message' => [
            'id' => $this->message->id,
            'body' => $this->message->body,

            'user' => [
                'name' => $this->message->user->name
            ],

            'user_id' => $this->message->user_id,

            'created_at' => $this->message->created_at
        ]
    ];
}

}