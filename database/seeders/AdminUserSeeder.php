<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'worldchanger@Christ.com'],
            [
                'name'     => 'System Administrator',
                'username' => 'worldchanger', 
                'password' => Hash::make('password123Love'),
                'role'     => 'administrator', 
            ]
        );
    }
}