<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'body',
        'user_id',
        'group_id',
        'topic_id',
        'reply_to_message_id',
        'thread_id',
        'course_code',
        'ai_description',
        'deleted_by',
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
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'thread_id');
    }

    /**
     * All messages inside this thread
     */
    public function threadMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    /**
     * The exact message this message replied to
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    /**
     * Direct replies under this message
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