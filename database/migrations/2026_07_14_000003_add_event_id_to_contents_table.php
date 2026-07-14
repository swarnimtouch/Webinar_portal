<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('id')
                ->constrained('events')->cascadeOnDelete();
            $table->index(['event_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropIndex(['event_id', 'slug']);
            $table->dropConstrainedForeignId('event_id');
        });
    }
};
