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
        User::create([
            'name' => 'System Administrator',
            'email' => 'worldchanger@Christ.com', // 📥 Your custom username/email
            'username' => 'worldchanger', 
            'password' => Hash::make('password123Love'), // 🔒 Your custom key
            'role' => 'administrator', 
        ]);
    }
}