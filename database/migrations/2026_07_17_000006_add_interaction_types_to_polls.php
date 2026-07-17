<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->string('interaction_type', 30)->default('single_choice')->after('question');
            $table->unsignedTinyInteger('rating_max')->default(5)->after('interaction_type');
        });

        DB::statement('ALTER TABLE polls MODIFY answers JSON NULL');
        Schema::table('user_poll_answers', function (Blueprint $table) {
            $table->dropForeign(['answer_id']);
        });
        DB::statement('ALTER TABLE user_poll_answers MODIFY answer_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE user_poll_answers MODIFY answer TEXT NOT NULL');
        Schema::table('user_poll_answers', function (Blueprint $table) {
            $table->foreign('answer_id')->references('id')->on('poll_answers')->nullOnDelete();
        });

        DB::table('polls')->update(['interaction_type' => 'single_choice']);
    }

    public function down(): void
    {
        Schema::table('user_poll_answers', function (Blueprint $table) {
            $table->dropForeign(['answer_id']);
        });
        DB::statement("DELETE FROM user_poll_answers WHERE answer_id IS NULL");
        DB::statement('ALTER TABLE user_poll_answers MODIFY answer VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE user_poll_answers MODIFY answer_id BIGINT UNSIGNED NOT NULL');
        Schema::table('user_poll_answers', function (Blueprint $table) {
            $table->foreign('answer_id')->references('id')->on('poll_answers')->cascadeOnDelete();
        });
        DB::statement("UPDATE polls SET answers = JSON_ARRAY() WHERE answers IS NULL");
        DB::statement('ALTER TABLE polls MODIFY answers JSON NOT NULL');
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['interaction_type', 'rating_max']);
        });
    }
};
