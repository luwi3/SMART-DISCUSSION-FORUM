<?php
namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'password',
        'role',
        'agreed_to_rules',
        'status',
        'lastCommDate',
        'blacklist_until',
        'student_category',
    ];

    // ...keep the rest of your existing methods exactly as they are...

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the study groups associated with the user for broadcast channel validation.
     */
    public function groups(): BelongsToMany
    {
        // Fallback target check depending on whether your model is Group or GroupDiscussion
        if (class_exists(\App\Models\GroupDiscussion::class)) {
            return $this->belongsToMany(GroupDiscussion::class, 'group_memberships', 'user_id', 'group_discussion_id');
        }
        
        return $this->belongsToMany(Group::class, 'group_memberships', 'user_id', 'group_id');
    }

    /**
     * Get the lecturer profile associated with the user.
     */
    public function lecturer()
    {
        return $this->hasOne(Lecturer::class, 'user_id', 'id');
    }

    /**
     * Link from User to Student profile.
     * We must specify 'user_id' as the foreign key on the students table,
     * and 'id' as the local key on our users table.
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }
}
