<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizAndQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = DB::table('lecturers')->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'LEC/2026/11';

        $quizID = DB::table('quizzes')->insertGetId([
            'staffNo' => $staffNo,
            'courseCode' => 'BIT 2201',
            'title' => 'Advanced Relational Database Systems Test',
            'duration' => 10,
            'startTime' => now(), 
            'expiryTime' => now()->addHours(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'quizID' => $quizID,
                'question_text' => 'Which database normalization form rules out partial functional dependencies on composite keys?',
                'option_a' => 'First Normal Form (1NF)',
                'option_b' => 'Second Normal Form (2NF)',
                'option_c' => 'Third Normal Form (3NF)',
                'option_d' => 'Boyce-Codd Normal Form (BCNF)',
                'correct_option' => 'B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quizID' => $quizID,
                'question_text' => 'What type of structural lock prevents concurrent transactions from modifying data resources?',
                'option_a' => 'Shared Lock',
                'option_b' => 'Exclusive Lock',
                'option_c' => 'Intent Lock',
                'option_d' => 'Optimistic Isolation State',
                'correct_option' => 'B',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}