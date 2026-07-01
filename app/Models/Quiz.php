<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'quizID';

    protected $fillable = [
        'staffNo',
        'courseCode',
        'title',
        'duration',
        'startTime',
        'expiryTime',
    ];
}