<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('messages', function (Blueprint $table) {
        $table->foreignId('group_id')
              ->nullable()
              ->after('topic_id')
              ->constrained('group_discussions')
              ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('messages', function (Blueprint $table) {
        $table->dropForeign(['group_id']);
        $table->dropColumn('group_id');
    });
}};
