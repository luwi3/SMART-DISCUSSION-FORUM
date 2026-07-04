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
        Schema::create('students', function (Blueprint $table) {
           // $table->id();
           // $table->timestamps();
           $table->string('regNo')->primary(); // PK from SDD
             $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to login account
             $table->string('courseCode'); // Critical for matching quizzes!
             $table->string('status')->default('active');
             $table->date('lastCommDate')->nullable();
             $table->dateTime('banExpiry')->nullable();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
