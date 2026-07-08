<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topic_participations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('topic_id'); 
            $table->unsignedBigInteger('user_id');  
            $table->decimal('marks_earned', 5, 2)->default(0.00); 
            $table->timestamps();

            $table->unique(['topic_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topic_participations');
    }
};