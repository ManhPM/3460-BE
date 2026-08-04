<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding shipping rates (50,000 VND) for all provinces...');

        $provinces = DB::table('provinces')->get();

        if ($provinces->isEmpty()) {
            $this->command->warn('No provinces found in `provinces` table! Please seed provinces first.');
            return;
        }

        $count = 0;
        foreach ($provinces as $province) {
            DB::table('shipping_rates')->updateOrInsert(
                [
                    'province_id' => $province->id,
                    'ward_id'     => null,
                ],
                [
                    'price'      => 50000,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $count++;
        }

        $this->command->info("Seeded shipping rates (50,000) successfully for {$count} provinces.");
    }
}
