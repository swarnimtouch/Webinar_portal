<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        $used = [];

        DB::table('events')->orderBy('id')->get(['id', 'name'])->each(function ($event) use (&$used) {
            $base = Str::slug($event->name) ?: 'event-' . $event->id;
            $slug = $base;
            $suffix = 2;

            while (isset($used[$slug])) {
                $slug = $base . '-' . $suffix++;
            }

            $used[$slug] = true;
            DB::table('events')->where('id', $event->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        // Previous domain-derived slugs cannot be restored reliably.
    }
};
