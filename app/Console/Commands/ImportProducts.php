<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate products, orders, flashsales and import from tonkho.xlsx';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting truncation...');
        $this->truncateTables();
        $this->info('Truncation completed.');

        $this->info('Starting import...');
        $this->importData();
        $this->info('Import completed.');

        return \Illuminate\Console\Command::SUCCESS;
    }

    protected function truncateTables()
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        
        $tables = [
            'flash_sales_products',
            'flash_sales',
            'order_details',
            'orders',
            'products_variations_variations',
            'products_variations',
            'products_attributes_variations',
            'products_attributes',
            'products_categories',
            'products',
            'admin_inventories',
            'shopping_cart',
            'wishlists',
            'reviews',
            'attributes_variations',
            'attributes',
        ];

        foreach ($tables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                \Illuminate\Support\Facades\DB::table($table)->truncate();
                $this->line("Truncated: $table");
            }
        }

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    protected function importData()
    {
        $file = base_path('tonkho.xlsx');
        if (!file_exists($file)) {
            $this->error('File tonkho.xlsx not found at ' . $file);
            return;
        }

        $importObject = new class implements \Maatwebsite\Excel\Concerns\WithCalculatedFormulas {};
        $data = \Maatwebsite\Excel\Facades\Excel::toArray($importObject, $file);
        $rows = $data[0] ?? [];
        
        // Skip header
        array_shift($rows);

        $attribute = \App\Models\Attribute::create([
            'name' => 'Đơn vị tính',
            'slug' => 'don-vi-tinh',
            'type' => 1,
            'position' => 0,
        ]);

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $index => $row) {
            try {
                $productName = trim($row[0] ?? '');
                $qty = (int)($row[1] ?? 0);
                $unitRaw = trim($row[2] ?? '');
                
                // Parse price: handle formats like 50.000 or 50,000
                $priceRaw = $row[3] ?? 0;
                if (is_string($priceRaw)) {
                    $priceRaw = str_replace(['.', ','], '', $priceRaw);
                }
                $price = (float)$priceRaw;

                if (empty($productName)) {
                    $bar->advance();
                    continue;
                }

                $categoryId = $this->getCategoryIdByName($productName);

                // Check if variation name is empty
                if (empty($unitRaw)) {
                    // Simple Product Case
                    $product = \App\Models\Product::where('name', $productName)->first();
                    if (!$product) {
                        $productData = [
                            'name' => $productName,
                            'slug' => \Illuminate\Support\Str::slug($productName) . '-' . time() . '-' . $index,
                            'price' => $price * 1.2,
                            'promotion_price' => $price,
                            'type' => \App\Enums\Product\ProductType::Simple,
                            'is_active' => 1,
                            'avatar' => '/userfiles/images/popup/logo.png',
                            'gallery' => ['/userfiles/images/popup/logo.png'],
                        ];

                        $product = \App\Models\Product::create($productData);
                    } else {
                        // Update price if product exists
                        $product->update(['price' => $price * 1.2, 'promotion_price' => $price]);
                    }

                    // Link to category
                    $product->categories()->syncWithoutDetaching([$categoryId]);

                    // Update AdminInventory for simple product
                    $inventory = \App\Models\AdminInventory::where([
                        'admin_id' => 2,
                        'product_id' => $product->id,
                        'product_variation_id' => null,
                    ])->first();

                    if ($inventory) {
                        $inventory->update(['qty' => $inventory->qty + $qty]);
                    } else {
                        \App\Models\AdminInventory::create([
                            'admin_id' => 2,
                            'product_id' => $product->id,
                            'product_variation_id' => null,
                            'qty' => $qty,
                        ]);
                    }

                } else {
                    // Variable Product Case
                    // Standardize unit: Gói, Lon, Hộp...
                    $unit = ucfirst(mb_strtolower($unitRaw));

                    // Find or create attribute variation (e.g., "Gói")
                    $attrVariation = $attribute->variations()->firstOrCreate(
                        ['name' => $unit],
                        ['slug' => \Illuminate\Support\Str::slug($unit), 'position' => 0]
                    );

                    // Find or create product
                    $product = \App\Models\Product::where('name', $productName)->first();
                    if (!$product || $product->type == \App\Enums\Product\ProductType::Simple) {
                        $productData = [
                            'name' => $productName,
                            'slug' => \Illuminate\Support\Str::slug($productName) . '-' . time() . '-' . $index,
                            'price' => $price * 1.2,
                            'promotion_price' => $price,
                            'type' => \App\Enums\Product\ProductType::Variable,
                            'is_active' => 1,
                            'avatar' => '/userfiles/images/popup/logo.png',
                            'gallery' => ['/userfiles/images/popup/logo.png'],
                        ];

                        if (!$product) {
                            $product = \App\Models\Product::create($productData);
                        } else {
                            $product->update(['type' => \App\Enums\Product\ProductType::Variable, 'price' => $price * 1.2, 'promotion_price' => $price]);
                        }
                        
                        // Link product to attribute "Đơn vị tính"
                        $product->attributes()->syncWithoutDetaching([$attribute->id => ['position' => 0]]);

                        // Link to category
                        $product->categories()->syncWithoutDetaching([$categoryId]);
                    }

                    // Create product variation
                    $variationData = [
                        'price' => $price * 1.2,
                        'promotion_price' => $price,
                        'is_active' => 1,
                        'position' => 0,
                        'image' => '/userfiles/images/popup/logo.png',
                    ];

                    // Check if variation already exists for this unit
                    $productVariation = $product->productVariations()
                        ->whereHas('attributeVariations', function ($q) use ($attrVariation) {
                            $q->where('attributes_variations.id', $attrVariation->id);
                        })->first();

                    if (!$productVariation) {
                        $productVariation = $product->productVariations()->create($variationData);
                        // Link product variation to specific unit (e.g., this variation is a "Gói")
                        $productVariation->attributeVariations()->attach($attrVariation->id);
                    } else {
                        // Update price if variation exists
                        $productVariation->update(['price' => $price * 1.2, 'promotion_price' => $price]);
                    }

                    // Update AdminInventory
                    $inventory = \App\Models\AdminInventory::where([
                        'admin_id' => 2,
                        'product_id' => $product->id,
                        'product_variation_id' => $productVariation->id,
                    ])->first();

                    if ($inventory) {
                        $inventory->update(['qty' => $inventory->qty + $qty]);
                    } else {
                        \App\Models\AdminInventory::create([
                            'admin_id' => 2,
                            'product_id' => $product->id,
                            'product_variation_id' => $productVariation->id,
                            'qty' => $qty,
                        ]);
                    }
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error at row " . ($index + 2) . ": " . $e->getMessage());
                // Continue to next row or exit? Let's continue.
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function getCategoryIdByName($name)
    {
        $name = mb_strtolower($name);

        $mapping = [
            53 => ['mì', 'bún', 'miến', 'phở', 'phơ', 'nui', 'hủ tiếu', 'bánh phở', 'bánh đa', 'cháo', 'bánh tráng', 'bánh hỏi'],
            55 => ['nước mắm', 'nước chấm', 'tương ớt', 'tương cà', 'xì dầu', 'mắm tôm', 'mắm nêm', 'sate', 'sa tế', 'nước tương', 'mắm cá', 'xốt me', 'tương nếp'],
            59 => ['sữa', 'sua', 'milo', 'vinamilk', 'th true milk', 'nuti', 'fami', 'ông thọ', 'sữa đặc'],
            61 => ['nước ngọt', 'nuoc ngot', 'coca', 'pepsi', 'sting', 'redbull', 'bò húc', 'fanta', 'mirinda', 'sprite', 'c2', 'number 1', 'carabao', 'tăng lực', 'bí đao', 'sâm', 'me', 'chanh dây', 'dừa', 'yến', 'sầm', 'sữa bắp', 'mía tắc', 'trà xanh o độ', 'hổ kaka'],
            62 => ['cà phê', 'ca phe', 'coffee', 'g7', 'nescafe', 'vinacafe', 'b\'fast', 'ngũ cốc'],
            63 => ['trà', 'tra', 'lipton', 'oolong', 'cozy', 'thái xanh'],
            66 => ['thịt', 'thit', 'heo', 'bò', 'bo', 'gà', 'ga', 'vịt', 'vit', 'chân gà', 'cánh gà', 'xương', 'sườn', 'dạ dày', 'phèo', 'tràng', 'tim', 'lưỡi', 'móng', 'giò sống', 'mỡ', 'bao tử', 'lòng non', 'bắp heo', 'diềm gan', 'dồi', 'ếch'],
            67 => ['đồ hộp', 'do hop', 'xúc xích', 'xuc xich', 'pate', 'cá mòi', '3 cô gái', 'lương khô', 'nhãn hộp', 'vải hộp', 'mít hộp'],
            70 => ['giò', 'chả', 'gio', 'cha', 'chả lụa', 'cha lua', 'nem', 'mọc'],
            73 => ['cá', 'ca', 'tôm', 'tom', 'cua', 'mực', 'muc', 'rau', 'củ', 'quả', 'trái cây', 'bắp', 'khoai', 'nấm', 'mộc nhĩ', 'ốc', 'lươn', 'sầu riêng', 'vải', 'mít', 'sấu', 'dưa muối', 'cà pháo', 'nhộng', 'bạch tuộc', 'tép', 'măng', 'lạc', 'đậu', 'đỗ', 'sen', 'ớt', 'hải sản', 'hành muối', 'sứa'],
            60 => ['bánh', 'kẹo', 'banh', 'keo', 'chocolate', 'socola', 'snack', 'bimbim', 'oishi', 'mứt', 'rito', 'quế', 'bánh đậu xanh', 'dừa nướng', 'thạch', 'rau câu', 'hướng dương', 'cốm', 'afc', 'tăm cay'],
            64 => ['gia vị', 'gia vi', 'hạt nêm', 'hat nem', 'bột ngọt', 'bot ngot', 'muối', 'muoi', 'đường', 'duong', 'dầu ăn', 'dau an', 'ajinomoto', 'knorr', 'maggi', 'bột canh', 'xốt', 'sốt', 'mẻ', 'nước màu', 'tiêu', 'nghệ', 'riềng', 'tỏi', 'thính', 'giấm', 'bột bắp', 'bột gạo', 'bột năng', 'bột nếp', 'dầu điều', 'bột sương sáo', 'bột mì', 'thính gạo', 'aji-quick', 'chẩm chéo'],
        ];

        foreach ($mapping as $categoryId => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    return $categoryId;
                }
            }
        }

        return 72; // Các sản phẩm khác
    }
}
