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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->string('domain')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->comment('Event venue email');
            $table->string('phone')->nullable()->comment('Event venue phone number');
            $table->longText('description')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('player_id')->nullable();
            $table->string('player_type')->nullable();
            $table->text('player_iframe')->nullable();
            $table->date('publish_date')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->dateTime('active_user_from')->nullable();
            $table->dateTime('active_user_to')->nullable();
            $table->boolean('is_log_attendance')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
