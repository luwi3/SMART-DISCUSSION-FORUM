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
        Schema::create('quiz_submissions', function (Blueprint $table) {
           // $table->id();
           // $table->timestamps();
            $table->id(); // Submission record ID
    
    // 🎓 Student connection
    $table->string('regNo');
    $table->foreign('regNo')->references('regNo')->on('students')->onDelete('cascade');
    
    // 📝 Quiz connection (Matching your custom 'quizID' key type)
    $table->unsignedBigInteger('quizID');
    $table->foreign('quizID')->references('quizID')->on('quizzes')->onDelete('cascade');
    
    // 📊 SDD Specific Attributes
    $table->integer('marks');
    $table->dateTime('timeSubmitted');
    $table->boolean('autoSubmit')->default(false);
    
    $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_submissions');
    }
};
