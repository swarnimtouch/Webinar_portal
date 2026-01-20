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
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('player_id')->nullable();
            $table->string('player_type');
            $table->longText('url');
            $table->date('publish_date')->nullable();
            $table->dateTime('event_start_time')->nullable();
            $table->dateTime('event_end_time')->nullable();
            $table->dateTime('active_from_date')->nullable();
            $table->dateTime('active_to_date')->nullable();
            $table->boolean('user_attendance')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
