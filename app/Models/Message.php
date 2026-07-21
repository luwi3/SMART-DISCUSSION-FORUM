<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'body',
        'user_id',
        'group_id',
        'topic_id',
        'reply_to_message_id',
        'thread_id',
    ];


    /**
     * The user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * The main message that started this thread
     *
     * Example:
     * Message 1: "Explain Laravel"
     * Message 2: "Laravel uses MVC"
     * Message 3: "Can you explain MVC?"
     *
     * Messages 2 and 3 belong to thread_id = 1
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'thread_id');
    }


    /**
     * All messages inside this thread
     *
     * Gets every message that belongs to the same thread
     */
    public function threadMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }


    /**
     * The exact message this message replied to
     *
     * Example:
     * Message 5 replies directly to Message 2
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }


    /**
     * Direct replies under this message
     *
     * Example:
     * Message 1
     *    ├── Message 2
     *    └── Message 3
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_message_id');
    }
    

    /**
     * Study group relationship
     */
    public function groupDiscussion(): BelongsTo
    {
        return $this->belongsTo(GroupDiscussion::class, 'group_id');
    }


    /**
     * Topic/course discussion relationship
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}