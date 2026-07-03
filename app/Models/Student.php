<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // 🔑 SDD Requirement: Set custom primary key attributes
    protected $primaryKey = 'regNo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'regNo',
        'user_id',
        'courseCode',
        'status',
        'lastCommDate',
        'banExpiry'
    ];

    /**
     * Connection back to the main login User account.
     * Explicitly defines the foreign key 'user_id' and owner key 'id'
     * to keep Eloquent from brokenly assuming 'user_regNo'.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 📊 Link to Quiz Submissions.
     * Connects this student's unique custom string registration number 
     * to their individual system-marked scores.
     */
    public function quizSubmissions()
    {
        return $this->hasMany(QuizSubmission::class, 'regNo', 'regNo');
    }
}