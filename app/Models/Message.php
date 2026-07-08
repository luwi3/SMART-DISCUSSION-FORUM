<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['body', 'user_id', 'group_discussion_id', 'topic_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groupDiscussion(): BelongsTo
    {
        return $this->belongsTo(GroupDiscussion::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}