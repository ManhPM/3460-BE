<?php

namespace App\Api\AdminV1\Services\FlashSale;

use App\Api\AdminV1\Repositories\FlashSale\FlashSaleRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlashSaleService
{
    protected $repository;
    protected array $data = [];

    public function __construct(FlashSaleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Convert datetime from ISO format to MySQL datetime format
     */
    protected function formatDateTime($dateTime)
    {
        if (empty($dateTime)) {
            return null;
        }

        try {
            // Try to parse ISO format or any datetime format
            $carbon = Carbon::parse($dateTime);
            // Return MySQL datetime format: Y-m-d H:i:s
            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // If parsing fails, return as is
            return $dateTime;
        }
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $this->data = $data;

            // Format datetime fields for MySQL
            if (isset($this->data['start_time'])) {
                $this->data['start_time'] = $this->formatDateTime($this->data['start_time']);
            }
            if (isset($this->data['end_time'])) {
                $this->data['end_time'] = $this->formatDateTime($this->data['end_time']);
            }

            // Create flash sale
            $flashSale = $this->repository->create($this->data);

            // Create flash sale details (products)
            $this->createAndStoreDataFlashSaleDetails($flashSale, []);

            DB::commit();
            return $flashSale->load(['details.product', 'details.product_variation.attributeVariations']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create flash sale: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $this->data = $data;

            // Format datetime fields for MySQL
            if (isset($this->data['start_time'])) {
                $this->data['start_time'] = $this->formatDateTime($this->data['start_time']);
            }
            if (isset($this->data['end_time'])) {
                $this->data['end_time'] = $this->formatDateTime($this->data['end_time']);
            }

            // Update flash sale
            $flashSale = $this->repository->update($id, $this->data);

            // Store old details to preserve sold quantity
            $oldDetails = [];
            $flashSale->details->each(function ($detail) use (&$oldDetails) {
                $key = $detail->product_id . '_' . ($detail->product_variation_id ?? 'null');
                $oldDetails[$key] = $detail->sold ?? 0;
                $detail->delete();
            });

            // Create new flash sale details (products) and preserve sold quantity if exists
            $this->createAndStoreDataFlashSaleDetails($flashSale, $oldDetails);

            DB::commit();
            return $flashSale->load(['details.product', 'details.product_variation.attributeVariations']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update flash sale: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Create and store flash sale details (products)
     * @param array $oldDetails Array of old details with sold quantity, key format: productId_variationId
     */
    private function createAndStoreDataFlashSaleDetails($flashSale, array $oldDetails = []): void
    {
        $productIds = $this->data['product_id'] ?? [];
        $qtys = $this->data['qty'] ?? [];
        $flashsalePrices = $this->data['flashsale_price'] ?? [];
        $productVariationIds = $this->data['product_variation_id'] ?? [];
        $productVariationFlashsalePrices = $this->data['product_variation_flashsale_price'] ?? [];
        $productVariationQtys = $this->data['product_variation_qty'] ?? [];

        foreach ($productIds as $index => $productId) {
            // Kiểm tra xem product này có variation không (dựa vào product_variation_id[index])
            $variationId = $productVariationIds[$index] ?? null;
            $hasVariation = !empty($variationId) && $variationId !== '' && $variationId !== '0' && $variationId !== 0;

            // Get sold quantity from old details if exists
            $detailKey = $productId . '_' . ($variationId ?? 'null');
            $soldQty = $oldDetails[$detailKey] ?? 0;

            if ($hasVariation) {
                // Sản phẩm có biến thể
                // Ưu tiên lấy từ product_variation_qty, nếu không có thì lấy từ qty
                $qty = null;
                if (isset($productVariationQtys[$index]) && $productVariationQtys[$index] !== null) {
                    $qty = $productVariationQtys[$index];
                } elseif (isset($qtys[$index]) && $qtys[$index] !== null) {
                    $qty = $qtys[$index];
                } else {
                    $qty = 0;
                }

                $flashsalePrice = $productVariationFlashsalePrices[$index] ?? 0;

                $flashSale->details()->create([
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'qty' => $qty,
                    'flashsale_price' => $flashsalePrice,
                    'flash_sale_id' => $flashSale->id,
                    'sold' => $soldQty, // Preserve sold quantity from old detail if exists
                ]);
            } else {
                // Sản phẩm không có biến thể (simple product)
                $qty = $qtys[$index] ?? 0;
                $flashsalePrice = $flashsalePrices[$index] ?? 0;

                $flashSale->details()->create([
                    'product_id' => $productId,
                    'product_variation_id' => null,
                    'qty' => $qty,
                    'flashsale_price' => $flashsalePrice,
                    'flash_sale_id' => $flashSale->id,
                    'sold' => $soldQty, // Preserve sold quantity from old detail if exists
                ]);
            }
        }
    }
}
