<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This tells Laravel to look into your AdminUserSeeder file and run it
        $this->call([
            AdminUserSeeder::class,
            LecturerSeeder::class,
            StudentSeeder::class,
            QuizAndQuestionsSeeder::class,
        ]);
    }
}