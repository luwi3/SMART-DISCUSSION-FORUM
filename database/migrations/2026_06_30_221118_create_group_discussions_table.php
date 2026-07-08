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
            $table->string('name');
            $table->timestamps();
        });
    } // ◄ Check this: Your original file is likely missing this closing curly brace!

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_discussions');
    }
};