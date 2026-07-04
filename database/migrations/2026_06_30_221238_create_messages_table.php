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
        Schema::create('messages', function (Blueprint $table) {
           $table->id();
        $table->text('body'); // The actual text message chat bubble
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The sender
        
        // These are nullable because a message belongs to EITHER a group OR a topic
        $table->foreignId('group_discussion_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('topic_id')->nullable()->constrained()->onDelete('cascade');
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
