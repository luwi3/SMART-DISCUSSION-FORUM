<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Quiz extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'quizID';

    // Specify if your primary key is not an auto-incrementing integer (e.g., if it's a string UUID)
    // public $incrementing = true; 

    protected $fillable = [
        'staffNo',
        'courseCode',
        'title',
        'duration',
        'startTime',
        'expiryTime',
    ];

    /**
     * Get the questions associated with the quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'quizID', 'quizID');
    }

    /**
     * Fetch submissions directly using the query builder.
     * This provides a clean fallback since we are using DB table operations in the controller.
     */
    public function submissions()
    {
        // We return a pseudo-relationship structure or direct reference to keep withCount clean.
        // If you create a QuizSubmission model later, swap this out for: return $this->hasMany(QuizSubmission::class, 'quizID', 'quizID');
        
        return $this->hasMany(Question::class, 'quizID', 'quizID') 
            ->from('quiz_submissions'); // Directing Eloquent to read from the submissions table instead
    }
}