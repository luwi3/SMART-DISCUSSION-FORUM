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
        Schema::create('group_memberships', function (Blueprint $table) {
            $table->id();
            
            // 👥 Link to the user joining the group
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 🏢 Link to the specific group being joined
            $table->foreignId('group_discussion_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();

            // 🛑 Safety Guard: Enforces that a user can only occupy ONE row per group
            $table->unique(['user_id', 'group_discussion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_memberships');
    }
};