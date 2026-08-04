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
            $fsDetails = $product->flash_sale_details 
                ?? ($product->is_flash_sale ? $product->is_flash_sale->details()->where('product_id', $product->id)->get() : collect());

            if ($fsDetails && $fsDetails->isNotEmpty()) {
                $data['flashsale_sold']  = (int) $fsDetails->sum('sold');
                $data['flashsale_qty']   = (int) $fsDetails->sum('qty');
                $data['flashsale_price'] = (float) $fsDetails->min('flashsale_price');
                $data['is_flash_sale']   = true;
            } else {
                $data['flashsale_sold']  = 0;
                $data['flashsale_qty']   = 0;
                $data['flashsale_price'] = 0;
                $data['is_flash_sale']   = false;
            }
            return $data;
        });
    }
}
