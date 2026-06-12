<?php

namespace App\Admin\Services\FlashSale;

use  App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use App\Admin\Repositories\Product\ProductRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class FlashSaleService implements FlashSaleServiceInterface
{

    protected array $data;

    protected FlashSaleRepositoryInterface $repository;
    protected ProductRepository $productRepository;

    public function __construct(
        FlashSaleRepositoryInterface $repository,
        ProductRepository $productRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }


    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $flashSale = $this->repository->update($this->data['id'], $this->data);
            $flashSale->details->each(function ($detail) {
                $detail->delete();
            });
            $this->createAndStoreDataFlashSaleDetails($flashSale);
            DB::commit();

            return $flashSale;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to update flash sale:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }


    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $flashSale = $this->repository->create($this->data);
            $this->createAndStoreDataFlashSaleDetails($flashSale);
            DB::commit();

            return $flashSale;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to create flash sale:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function createAndStoreDataFlashSaleDetails(object $flashSale): void
    {
        $productIds = $this->data['product_id'] ?? [];
        $qtys = $this->data['qty'] ?? [];
        $flashsalePrices = $this->data['flashsale_price'] ?? [];
        $productVariationIds = $this->data['product_variation_id'] ?? [];
        $productVariationFlashsalePrices = $this->data['product_variation_flashsale_price'] ?? [];

        foreach ($productIds as $index => $productId) {
            // Kiểm tra xem product này có variation không (dựa vào product_variation_id[index])
            $variationId = $productVariationIds[$index] ?? null;
            $hasVariation = !empty($variationId) && $variationId !== '' && $variationId !== '0';

            if ($hasVariation) {
                // Sản phẩm có biến thể
                $flashSale->details()->create([
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'qty' => $qtys[$index] ?? 0,
                    'flashsale_price' => $productVariationFlashsalePrices[$index] ?? 0,
                    'flash_sale_id' => $flashSale->id,
                ]);
            } else {
                // Sản phẩm không có biến thể (simple product)
                $flashSale->details()->create([
                    'product_id' => $productId,
                    'product_variation_id' => null,
                    'qty' => $qtys[$index] ?? 0,
                    'flashsale_price' => $flashsalePrices[$index] ?? 0,
                    'flash_sale_id' => $flashSale->id,
                ]);
            }
        }
    }
}
