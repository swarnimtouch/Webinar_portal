<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')
                ->constrained('companies')->nullOnDelete();
        });

        $domains = DB::table('events')->whereNotNull('domain')
            ->where('domain', '!=', '')->pluck('domain')->unique();

        foreach ($domains as $domain) {
            $slug = Str::slug($domain);
            if ($slug === '') {
                continue;
            }

            $companyId = DB::table('companies')->insertGetId([
                'name' => Str::headline($domain),
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('events')->where('domain', $domain)
                ->update(['company_id' => $companyId]);
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::dropIfExists('companies');
    }
};
