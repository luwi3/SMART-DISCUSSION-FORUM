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
        Schema::create('group_discussions', function (Blueprint $table) {
            $table->id();
            // 🌟 Tracks the user who created the group
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->string('name');
            
            // 🌟 Matches the "Group Description" textarea field in your form
            $table->text('description'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_discussions');
    }
};