<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filled = Http::get('https://api.github.com/repos/tabler/tabler-icons/contents/icons/filled?ref=main');
        $outline = Http::get('https://api.github.com/repos/tabler/tabler-icons/contents/icons/outline?ref=main');

        if ($filled->successful() && $outline->successful()) {
            $filledIcons = $filled->json();
            $outlineIcons = $outline->json();
            foreach ($outlineIcons as $icon) {
                $name = pathinfo($icon['name'], PATHINFO_FILENAME);
                DB::table('icons')->insert([
                    'name' => 'ti ti-' . $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            foreach ($filledIcons as $icon) {
                $name = pathinfo($icon['name'], PATHINFO_FILENAME);
                DB::table('icons')->insert([
                    'name' => 'ti ti-' . $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('Icons seeded successfully!');
        } else {
            $this->command->error('Failed to fetch icons from GitHub.');
        }
    }
}
