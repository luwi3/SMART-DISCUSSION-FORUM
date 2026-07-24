<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary Key (userID)
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('phone')->nullable();
            $table->string('password'); // Securely hashed string
            
            // Core System Requirement Additions:
            // 🛡️ Active and defaulting to student since public sign-ups are students
            $table->enum('role', ['student', 'lecturer', 'administrator'])->default('student');
            
            $table->boolean('agreed_to_rules')->default(false); // Req 5[cite: 1]
            $table->enum('status', ['active', 'warned_1', 'warned_2', 'blacklisted'])->default('active'); // Req 4[cite: 1]
            $table->timestamp('blacklist_until')->nullable(); // Expiration timer for blacklisted users[cite: 1]
            $table->string('student_category')->nullable(); // Used to target quizzes (e.g., 'Level 100', 'IT-Major') (Req 10)[cite: 1]
            
            $table->rememberToken(); // Used by Laravel for state persistence
            $table->timestamps(); // Creates created_at and updated_at automatically
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
