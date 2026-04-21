<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('user_quiz_answers', 'user_poll_answers');

        Schema::table('user_poll_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('answer_id')->after('poll_id');
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('poll_answers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('user_poll_answers', 'user_quiz_answers');
        Schema::table('user_quiz_answers', function (Blueprint $table) {
            $table->dropForeign('user_quiz_answers_poll_id_foreign');
            $table->dropForeign('user_quiz_answers_answer_id_foreign');
            $table->dropColumn('answer_id');
        });
    }
};
