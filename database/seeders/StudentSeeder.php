<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {

        // 1. CREATE STUDENT 1 (John)
        $johnUserId = DB::table('users')->insertGetId([
            'name' => 'John Doe',
            'username' => 'john.doe',
            'email' => 'john@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('students')->insert([
            'regNo' => '2026/BIT/042',
            'user_id' => $johnUserId,
            
            'courseCode' => 'BIT 2201',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // 2. CREATE STUDENT 2 (Jane)
        $janeUserId = DB::table('users')->insertGetId([
            'name' => 'Jane Smithers',
            'username' => 'jane.smithers',
            'email' => 'jane@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('students')->insert([
            'regNo' => '2026/BIT/089',
            'user_id' => $janeUserId,
            
            'courseCode' => 'BIT 2201',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}