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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('background_image');
            $table->string('font_file');
            $table->integer('font_size');
            $table->string('font_color')->default('#000000');
            $table->boolean('is_bold')->default(0);
            $table->integer('start_x');
            $table->integer('end_x')->nullable();
            $table->integer('y');

            $table->enum('status', ['active','inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
