<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Message;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewReplyNotification extends Notification
{
    protected $reply;

    public function __construct(Message $reply)
    {
        $this->reply = $reply;
    }

    public function via(object $notifiable): array
    {
        // 🎯 'broadcast' pushes it instantly to WebSockets alongside the DB entry
        return ['database', 'broadcast'];
    }

    protected function payload(): array
    {
        $url = $this->reply->topic_id
            ? '/forum-workspace/topic/' . $this->reply->topic_id
            : '/chat';

        return [
            // NOTE: Laravel's broadcast channel overwrites a top-level 'type' key
            // with the notification's FQCN, so a distinct key is used here to
            // avoid the collision — see NewTopicNotification for the same fix.
            'category' => 'reply',
            'title' => 'New Reply',
            'message' => ($this->reply->user->name ?? 'Someone') . ' replied to your message',
            'url' => $url,
            'topic_id' => $this->reply->topic_id,
            'message_id' => $this->reply->id,
            'reply_to_message_id' => $this->reply->reply_to_message_id,
            'from_user_id' => $this->reply->user_id,
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * 🎯 This pushes real-time payloads down Laravel Echo channels immediately
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }
}
