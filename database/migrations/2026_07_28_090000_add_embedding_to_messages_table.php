<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Null until EmbedMessage processes it — also stays null for
            // trivial/short messages (see EmbedMessage) so they never
            // pollute a student's interest profile.
            $table->json('embedding')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
