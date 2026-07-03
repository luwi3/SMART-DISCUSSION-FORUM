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
            $table->id(); // Primary key for the submission record
            
            // Foreign key linking to the quizzes table (handles both id or quizID naming conventions)
            // Adjust 'quizzes' if your quiz table has a different name
            $table->unsignedBigInteger('quizID'); 
            
            // Student Registration Number string matching your students table format
            $table->string('regNo'); 
            
            // Marks scored (using an integer, or float/decimal if you allow half-marks)
            $table->integer('marks')->default(0); 
            
            // Timestamp of the exact moment they hit submit or timed out
            $table->timestamp('timeSubmitted')->nullable(); 
            
            // Boolean flags supporting both naming styles used in your Blade views
            $table->boolean('autoSubmit')->default(false);
            $table->boolean('is_submitted_automatically')->default(false);
            
            $table->timestamps();

            // Optional but highly recommended: Add foreign key constraints for data integrity
            // $table->foreign('quizID')->references('id')->on('quizzes')->onDelete('cascade');
            // $table->foreign('regNo')->references('regNo')->on('students')->onDelete('cascade');
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