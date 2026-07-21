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
        Schema::create('chat_views', function (Blueprint $table) {
            $table->id();
            // Links the tracking row to a specific user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Tracks the layout area: 'main', 'topic', or 'group'
            $table->string('chat_type'); 
            
            // Stores the model ID (Topic ID, Group ID, or 0 for Main Chat)
            $table->unsignedBigInteger('chat_id'); 
            
            // Stores the exact timestamp when they last cleared/viewed the room
            $table->timestamp('last_read_at');
            $table->timestamps();

            // Prevents duplicate rows for the same user checking the same chat context
            $table->unique(['user_id', 'chat_type', 'chat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_views');
    }
};