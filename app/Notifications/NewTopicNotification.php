<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Topic;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewTopicNotification extends Notification
{
    protected $topic;

    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function via(object $notifiable): array
    {
        // 🎯 Added 'broadcast' to push instantly to WebSockets alongside the DB entry
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            // NOTE: kept distinct from 'type' — Laravel's broadcast channel
            // overwrites a top-level 'type' key with the notification's FQCN,
            // so 'category' is used instead to survive the broadcast payload.
            'category' => 'topic',
            'title' => 'New Topic',
            'message' => ($this->topic->user->name ?? 'Someone') . ' created a new topic: ' . $this->topic->title,
            'url' => '/forum-workspace/topic/' . $this->topic->id,
            'topic_id' => $this->topic->id, // Passing raw ID so Javascript can match the highlight filter smoothly
        ];
    }

    /**
     * 🎯 This pushes real-time payloads down Laravel Echo channels immediately
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'category' => 'topic',
            'title' => 'New Topic',
            'message' => ($this->topic->user->name ?? 'Someone') . ' created a new topic: ' . $this->topic->title,
            'url' => '/forum-workspace/topic/' . $this->topic->id,
            'topic_id' => $this->topic->id,
        ]);
    }
}