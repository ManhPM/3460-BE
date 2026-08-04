<?php

namespace App\Api\V1\Http\Resources\Product;

use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Support\AuthSupport;

class AllProductResource extends ResourceCollection
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
                'is_contact_price' => $product->is_contact_price,
                'is_flash_sale' => false,
                'avatar' => asset($product->avatar),
                'avg_rating' => round($product->avg_rating, 1),
                'variant_name' => $variantName,
            ];
            if ($product->is_flash_sale) {
                $fsDetails = $product->is_flash_sale->details()
                    ->where('product_id', $product->id)
                    ->get();
                $data['flashsale_sold']  = (int) $fsDetails->sum('sold');
                $data['flashsale_qty']   = (int) $fsDetails->sum('qty');
                $data['flashsale_price'] = $fsDetails->isNotEmpty() ? (float) $fsDetails->min('flashsale_price') : 0;
                $data['is_flash_sale']   = true;
            } else {
                $data['flashsale_sold']  = 0;
                $data['flashsale_qty']   = 0;
                $data['flashsale_price'] = 0;
                $data['is_flash_sale']   = false;
            }

            if ($product->type == ProductType::Simple) {
                $data['price'] = $product->price ?? 0;
                $data['promotion_price'] = $product->promotion_price ?? 0;
            } elseif ($product->productVariations) {
                $prices = [];
                $promotion_prices = [];
                foreach ($product->productVariations as $variation) {
                    if (!is_null($variation->promotion_price)) {
                        $prices[] = $variation->promotion_price;
                        $promotion_prices[] = $variation->promotion_price;
                    } elseif (!is_null($variation->price)) {
                        $prices[] = $variation->price;
                        $promotion_prices[] = $variation->price;
                    }
                }

                $data['price'] = !empty($prices) ? max($prices) : 0;
                $data['promotion_price'] = !empty($promotion_prices) ? min($promotion_prices) : 0;
            } else {
                $data['price'] = 0;
                $data['promotion_price'] = 0;
            }
            return $data;
        });
    }
}
