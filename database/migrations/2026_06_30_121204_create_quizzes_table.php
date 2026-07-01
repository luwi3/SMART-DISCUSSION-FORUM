<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id('quizID'); // Primary Key
            $table->string('staffNo'); 
            $table->foreign('staffNo')->references('staffNo')->on('lecturers')->onDelete('cascade');
            $table->string('courseCode'); 
            $table->string('title');
            $table->dateTime('startTime'); 
            $table->dateTime('expiryTime');
            $table->integer('duration'); // in minutes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};