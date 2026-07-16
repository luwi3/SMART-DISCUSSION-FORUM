<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicParticipation extends Model
{
    protected $table = 'topic_participations';

    protected $fillable = [
        'topic_id',
        'user_id',
        'marks_earned'
    ];

    /**
     * Get the student/user that owns this participation mark.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the discussion topic associated with this mark.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}