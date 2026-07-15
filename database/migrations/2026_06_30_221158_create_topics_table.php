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
        Schema::create('topics', function (Blueprint $table) {
           $table->id();
        $table->string('title'); 
        $table->text('description');// The student's question title
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Tracks who started it
        $table->timestamps();    // Crucial! This tracks the 24-hour lifetime mark
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
