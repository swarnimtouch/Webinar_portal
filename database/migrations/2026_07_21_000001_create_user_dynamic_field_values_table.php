<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_dynamic_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dynamic_field_id')->constrained('dynamic_fields')->cascadeOnDelete();
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'dynamic_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dynamic_field_values');
    }
};
