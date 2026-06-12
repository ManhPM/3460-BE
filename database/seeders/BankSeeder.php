<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Gửi request đến API để lấy danh sách ngân hàng
        $response = Http::get('https://api.vietqr.io/v2/banks');

        if ($response->successful()) {
            $banks = $response->json()['data']; // Giả sử danh sách ngân hàng nằm trong 'data'

            foreach ($banks as $bank) {
                DB::table('banks')->insert([
                    'name' => $bank['name'],
                    'code' => $bank['code'],
                    'bin' => $bank['bin'],
                    'short_name' => $bank['short_name'],
                    'logo' => $bank['logo'], // Đường dẫn logo từ API
                    'is_active' => 0, // Mặc định là 0
                    'bank_account' => null, // Để trống
                    'bank_account_number' => null, // Để trống
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info('Banks seeded successfully!');
        } else {
            $this->command->error('Failed to fetch banks from the API.');
        }
    }
}
