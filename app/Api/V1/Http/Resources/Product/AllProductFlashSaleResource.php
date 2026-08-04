<?php

namespace App\Api\V1\Http\Resources\Product;

use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Support\AuthSupport;

class AllProductFlashSaleResource extends ResourceCollection
{
    use AuthSupport;
    public function toArray($request)
    {
        return  $this->collection->map(function ($product) {
            $variantName = 'Mặc định';
            if ($product->type == ProductType::Variable && $product->productVariations && $product->productVariations->isNotEmpty()) {
                $firstVariation = $product->productVariations->first();
                if ($firstVariation && $firstVariation->attributeVariations && $firstVariation->attributeVariations->isNotEmpty()) {
                    $variantName = $firstVariation->attributeVariations->pluck('name')->implode(', ');
                }
            }

            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'avatar' => asset($product->avatar),
                'avg_rating' => round($product->avg_rating, 1),
                'variant_name' => $variantName,
            ];
            // Đọc từ flash_sale_details đã được attach (FlashSaleResourceNoPaginate)
            // hoặc fallback query qua is_flash_sale nếu không có
            $flashSaleDetails = $product->flash_sale_details ?? null;

            if ($flashSaleDetails) {
                // Lấy detail của sản phẩm chính (product_variation_id = null)
                $mainDetail = $flashSaleDetails->firstWhere('product_variation_id', null);
                $data['flashsale_sold'] = $mainDetail?->sold ?? 0;
                $data['flashsale_qty']  = $mainDetail?->qty ?? 0;

                // Nếu có biến thể → lấy giá min từ các variation detail
                $variationDetails = $flashSaleDetails->whereNotNull('product_variation_id');
                if ($variationDetails->isNotEmpty()) {
                    $data['flashsale_price'] = $variationDetails->min('flashsale_price') ?? 0;
                } else {
                    $data['flashsale_price'] = $mainDetail?->flashsale_price ?? 0;
                }
            } else {
                // Fallback: query trực tiếp (dùng cho FlashSaleResource)
                $detail = $product->is_flash_sale?->details()->where('product_id', $product->id)->first();
                $data['flashsale_sold']  = $detail?->sold ?? 0;
                $data['flashsale_qty']   = $detail?->qty ?? 0;
                $data['flashsale_price'] = $detail?->flashsale_price ?? 0;
            }
            return $data;
        });
    }
}
