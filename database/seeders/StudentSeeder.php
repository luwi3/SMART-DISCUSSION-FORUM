<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREATE THE LECTURER USER & PROFILE
        $lecturerUserId = DB::table('users')->insertGetId([
            'name' => 'Dr. Alex Mukasa',
            'email' => 'lecturer@test.com',
            'password' => Hash::make('lecturer123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('lecturers')->insert([
            'staffNo' => 'LEC/2026/11',
            'user_id' => $lecturerUserId,
            'name' => 'Dr. Alex Mukasa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. CREATE STUDENT 1 (John)
        $johnUserId = DB::table('users')->insertGetId([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('students')->insert([
            'regNo' => '2026/BIT/042',
            'user_id' => $johnUserId,
            'name' => 'John Doe',
            'courseCode' => 'BIT 2201',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. CREATE STUDENT 2 (Jane)
        $janeUserId = DB::table('users')->insertGetId([
            'name' => 'Jane Smithers',
            'email' => 'jane@test.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('students')->insert([
            'regNo' => '2026/BIT/089',
            'user_id' => $janeUserId,
            'name' => 'Jane Smithers',
            'courseCode' => 'BIT 2201',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}