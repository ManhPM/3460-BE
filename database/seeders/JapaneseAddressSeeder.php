<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class JapaneseAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Japanese Address Seeder...');

        // Truncate tables
        $this->command->info('Truncating provinces and wards tables...');
        DB::table('wards')->truncate();
        DB::table('provinces')->truncate();
        $this->command->info('Tables truncated successfully.');

        // Fetch provinces from API
        $this->command->info('Fetching provinces from API...');
        $provincesUrl = 'https://japanese-addresses-v2.geoloniamaps.com/api/ja.json';

        try {
            $response = Http::timeout(60)->get($provincesUrl);

            if (!$response->successful()) {
                $this->command->error('Failed to fetch provinces. HTTP Status: ' . $response->status());
                return;
            }

            $data = $response->json();

            if (!isset($data['data']) || !is_array($data['data'])) {
                $this->command->error('Invalid API response format.');
                return;
            }

            $provinces = $data['data'];
            $this->command->info('Found ' . count($provinces) . ' provinces.');

            $bar = $this->command->getOutput()->createProgressBar(count($provinces));
            $bar->start();

            $totalWardsInserted = 0;
            $totalWardsSkipped = 0;
            $failedProvinces = [];

            foreach ($provinces as $provinceData) {
                // Extract province name (pref field)
                $provinceName = $provinceData['pref'] ?? null;

                if (!$provinceName) {
                    $bar->advance();
                    continue;
                }

                // Create province
                $province = Province::create([
                    'name' => $provinceName,
                ]);

                // Fetch wards for this province
                // URL encode the province name for API call (using rawurlencode for proper encoding)
                $encodedProvinceName = rawurlencode($provinceName);
                $wardsUrl = "https://japanese-addresses-v2.geoloniamaps.com/api/ja/{$encodedProvinceName}.json";

                try {
                    $wardsResponse = Http::timeout(30)->get($wardsUrl);

                    if ($wardsResponse->successful()) {
                        $wardsData = $wardsResponse->json();

                        // Debug: Check response structure
                        if (!isset($wardsData['data'])) {
                            $failedProvinces[] = "{$provinceName}: Missing 'data' key. Response keys: " . implode(', ', array_keys($wardsData));
                            continue;
                        }

                        if (!is_array($wardsData['data'])) {
                            $failedProvinces[] = "{$provinceName}: 'data' is not array. Type: " . gettype($wardsData['data']);
                            continue;
                        }

                        if (empty($wardsData['data'])) {
                            $failedProvinces[] = "{$provinceName}: Empty data array";
                            continue;
                        }

                        $wardsToInsert = [];
                        $insertedCount = 0;
                        $skippedCount = 0;
                        $seenWardNames = []; // Track unique ward names in current batch

                        foreach ($wardsData['data'] as $wardData) {
                            // Extract ward name from 'city' field in API response
                            // Example: {"code":131016,"city":"千代田区",...}
                            // Note: 'city' field contains both cities and wards
                            $wardName = $wardData['city'] ?? null;

                            if ($wardName) {
                                // Check if we've already seen this ward name in current batch
                                if (isset($seenWardNames[$wardName])) {
                                    $skippedCount++;
                                    continue;
                                }

                                // Check if ward already exists in database (unique: province_id + name)
                                $exists = DB::table('wards')
                                    ->where('province_id', $province->id)
                                    ->where('name', $wardName)
                                    ->exists();

                                if (!$exists) {
                                    $wardsToInsert[] = [
                                        'province_id' => $province->id,
                                        'name' => $wardName, // Store city name as ward name
                                    ];
                                    $seenWardNames[$wardName] = true; // Mark as seen in current batch
                                } else {
                                    $skippedCount++;
                                }
                            }
                        }

                        // Bulk insert wards for better performance
                        if (!empty($wardsToInsert)) {
                            try {
                                DB::table('wards')->insert($wardsToInsert);
                                $insertedCount = count($wardsToInsert);
                                $totalWardsInserted += $insertedCount;
                                $totalWardsSkipped += $skippedCount;
                            } catch (\Exception $dbException) {
                                $failedProvinces[] = "{$provinceName}: DB Insert failed - " . $dbException->getMessage();
                            }
                        }
                    } else {
                        $body = $wardsResponse->body();
                        $failedProvinces[] = "{$provinceName}: HTTP " . $wardsResponse->status() . " - " . substr($body, 0, 100);
                    }
                } catch (\Exception $e) {
                    $failedProvinces[] = "{$provinceName}: Exception - " . $e->getMessage();
                }

                // Small delay to avoid rate limiting
                usleep(100000); // 0.1 second delay

                $bar->advance();
            }

            $bar->finish();
            $this->command->newLine(2);

            if (!empty($failedProvinces)) {
                $this->command->error("Failed to fetch wards for " . count($failedProvinces) . " provinces:");
                // Show all failures, not just first 10
                foreach ($failedProvinces as $failed) {
                    $this->command->error("  - {$failed}");
                }
            }

            $provinceCount = Province::count();
            $wardCount = Ward::count();

            $this->command->info("Seeder completed!");
            $this->command->info("Total provinces: {$provinceCount}");
            $this->command->info("Total wards inserted: {$totalWardsInserted}");
            $this->command->info("Total wards skipped (duplicates): {$totalWardsSkipped}");
            $this->command->info("Total wards in database: {$wardCount}");

            if ($wardCount == 0 && $totalWardsInserted == 0) {
                $this->command->error("WARNING: No wards were inserted! Please check the API responses.");
            }
        } catch (\Exception $e) {
            $this->command->error('Error occurred: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
