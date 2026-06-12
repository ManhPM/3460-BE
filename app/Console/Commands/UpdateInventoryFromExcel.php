<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInventoryFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-inventory
                            {--file=Tồn kho shin.xlsx : Tên file Excel (đặt ở root project)}
                            {--admin_id=2 : ID của admin/chi nhánh cần cập nhật tồn kho}
                            {--mode=set : Chế độ cập nhật: set (ghi đè) hoặc add (cộng dồn)}
                            {--dry-run : Chạy thử, không thực sự lưu DB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật số lượng tồn kho từ file Excel (Tồn kho shin.xlsx) theo tên sản phẩm + loại biến thể (ĐVT). Không xóa, không import product mới.';

    // Thống kê
    protected int $updated   = 0;
    protected int $created   = 0;
    protected int $skipped   = 0;
    protected int $notFound  = 0;
    protected array $notFoundList = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $file    = base_path($this->option('file'));
        $adminId = (int) $this->option('admin_id');
        $mode    = $this->option('mode');   // 'set' | 'add'
        $dryRun  = $this->option('dry-run');

        // ── Validate ───────────────────────────────────────────
        if (!in_array($mode, ['set', 'add'])) {
            $this->error("Mode phải là 'set' hoặc 'add'. Nhận được: $mode");
            return Command::FAILURE;
        }

        if (!file_exists($file)) {
            $this->error("Không tìm thấy file: $file");
            return Command::FAILURE;
        }

        if ($dryRun) {
            $this->warn('[DRY-RUN] Chế độ thử nghiệm — KHÔNG thực sự ghi DB.');
        }

        $this->info("File  : $file");
        $this->info("Admin : $adminId");
        $this->info("Mode  : $mode");
        $this->newLine();

        // ── Đọc Excel ──────────────────────────────────────────
        $this->info('Đang đọc file Excel...');
        $importObject = new class implements \Maatwebsite\Excel\Concerns\WithCalculatedFormulas {};
        $data = \Maatwebsite\Excel\Facades\Excel::toArray($importObject, $file);
        $rows = $data[0] ?? [];

        // Bỏ dòng header (row 0)
        array_shift($rows);

        $total = count($rows);
        $this->info("Tổng số dòng dữ liệu: $total");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($rows as $index => $row) {
            try {
                // ── Đọc giá trị từ đúng cột ───────────────────
                // col4 = Tên hàng, col5 = Tồn kho, col6 = ĐVT
                $productName = trim($row[4] ?? '');
                $rawQty      = $row[5] ?? 0;
                $qty         = (is_numeric($rawQty) && (float) $rawQty == (int) $rawQty) ? (int) $rawQty : 0;
                $unitRaw     = isset($row[6]) ? trim($row[6]) : null;

                if (empty($productName)) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                // Standardize unit (Gói, Lon, Hộp...)
                $unit = (!empty($unitRaw)) ? ucfirst(mb_strtolower($unitRaw)) : null;

                $this->processRow(
                    productName: $productName,
                    qty: $qty,
                    unit: $unit,
                    adminId: $adminId,
                    mode: $mode,
                    dryRun: $dryRun,
                    rowNum: $index + 2,
                );

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Lỗi dòng " . ($index + 2) . ": " . $e->getMessage());
                $this->skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // ── Báo cáo ────────────────────────────────────────────
        $this->table(
            ['Kết quả', 'Số lượng'],
            [
                ['Cập nhật (update)',  $this->updated],
                ['Tạo mới record tồn', $this->created],
                ['Bỏ qua (empty)',     $this->skipped],
                ['Không tìm thấy',     $this->notFound],
            ]
        );

        if (!empty($this->notFoundList)) {
            $this->newLine();
            $this->warn('Danh sách không tìm thấy (tối đa 50 dòng đầu):');
            $displayList = array_slice($this->notFoundList, 0, 50);
            foreach ($displayList as $item) {
                $this->line("  • {$item}");
            }
        }

        return Command::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────

    protected function processRow(
        string $productName,
        int    $qty,
        ?string $unit,
        int    $adminId,
        string $mode,
        bool   $dryRun,
        int    $rowNum,
    ): void {
        // 1. Tìm product theo tên (case-insensitive, trim)
        $product = \App\Models\Product::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($productName))])->first();

        if (!$product) {
            $this->notFound++;
            $this->notFoundList[] = "Dòng $rowNum | Sản phẩm không tồn tại: \"$productName\"";
            return;
        }

        $productVariationId = null;

        if (!empty($unit)) {
            // Variable product → tìm variation có attribute variation name = unit (ĐVT)
            $variation = $product->productVariations()
                ->whereHas('attributeVariations', function ($q) use ($unit) {
                    $q->whereRaw('LOWER(TRIM(attributes_variations.name)) = ?', [mb_strtolower(trim($unit))]);
                })
                ->first();

            if (!$variation) {
                $this->notFound++;
                $this->notFoundList[] = "Dòng $rowNum | \"$productName\" — Không tìm thấy biến thể \"$unit\"";
                return;
            }

            $productVariationId = $variation->id;
        }

        // 2. Tìm hoặc tạo record AdminInventory
        $conditions = [
            'admin_id'            => $adminId,
            'product_id'          => $product->id,
            'product_variation_id' => $productVariationId,
        ];

        $inventory = \App\Models\AdminInventory::where($conditions)->first();

        if (!$dryRun) {
            if ($inventory) {
                $newQty = ($mode === 'add') ? ($inventory->qty + $qty) : $qty;
                $inventory->update(['qty' => $newQty]);
                $this->updated++;
            } else {
                \App\Models\AdminInventory::create(array_merge($conditions, ['qty' => $qty]));
                $this->created++;
            }
        } else {
            // Dry run: chỉ đếm
            if ($inventory) {
                $this->updated++;
            } else {
                $this->created++;
            }
        }
    }
}
