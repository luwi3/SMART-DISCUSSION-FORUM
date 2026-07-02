<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        // 👨‍🏫 Create Lecturer User account
        $lecturerUser = DB::table('users')->insertGetId([
            'name' => 'Dr. Alex Mukasa',
            'email' => 'lecturer@test.com',
            'password' => Hash::make('lecturer123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link to the Lecturers profile registry table
        DB::table('lecturers')->insert([
            'staffNo' => 'LEC/2026/11',
            'user_id' => $lecturerUser,
            'name' => 'Dr. Alex Mukasa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}