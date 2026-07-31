<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        // 👨‍🏫 Check if Lecturer User account already exists
        $existingUser = DB::table('users')->where('email', 'lecturer@test.com')->first();

        if (! $existingUser) {
            $lecturerUserId = DB::table('users')->insertGetId([
                'name'       => 'Dr. Alex Mukasa',
                'username'   => 'alex.mukasa',
                'email'      => 'lecturer@test.com',
                'password'   => Hash::make('lecturer123'),
                'role'       => 'lecturer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $lecturerUserId = $existingUser->id;
        }

        // Link to the Lecturers profile registry table if not already linked
        $existingLecturer = DB::table('lecturers')->where('staffNo', 'LEC/2026/11')->first();

        if (! $existingLecturer) {
            DB::table('lecturers')->insert([
                'staffNo'    => 'LEC/2026/11',
                'user_id'    => $lecturerUserId,
                'department' => 'Information Technology',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}