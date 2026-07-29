<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // True only for messages posted directly inside a topic room.
            // Messages that reach a topic by being AI-classified out of the
            // main chat keep this false, so they still show up in main chat —
            // only topic-native messages are excluded from it.
            $table->boolean('created_in_topic')->default(false)->after('topic_id');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('created_in_topic');
        });
    }
};
