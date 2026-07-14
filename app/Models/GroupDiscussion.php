<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupDiscussion extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * 👥 The members joined to this discussion group workspace roster.
     */
    public function members()
    {
        // Links users to group discussions via your pivot table
        return $this->belongsToMany(User::class, 'group_memberships', 'group_discussion_id', 'user_id')
                    ->withTimestamps();
    }
}