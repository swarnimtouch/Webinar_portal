<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAttributeInputIconsSeeder extends Seeder
{
    public function run()
    {
        $icons = [
            'Short Text (up to 70 characters)' => 'bi bi-fonts',
            'Long Text (up to 300 characters)' => 'bi bi-textarea-resize',
            'Single Select Answer' => 'bi bi-list-ul',
            'Multi Select Answer' => 'bi bi-ui-checks',
            'Date Field' => 'bi bi-calendar',
            'File Upload' => 'bi bi-upload',
        ];

        foreach ($icons as $name => $icon) {
            DB::table('attribute_inputs')
                ->where('input_name', $name)
                ->whereNull('icon')
                ->update(['icon' => $icon]);
        }
    }
}
