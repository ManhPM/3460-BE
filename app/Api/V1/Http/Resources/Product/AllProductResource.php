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
            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'is_contact_price' => $product->is_contact_price,
                'is_flash_sale' => false,
                'avatar' => asset($product->avatar),
                'avg_rating' => round($product->avg_rating, 1),
            ];
            if ($product->is_flash_sale) {
                $fsDetail = $product->is_flash_sale->details()
                    ->where('product_id', $product->id)
                    ->whereNull('product_variation_id')
                    ->first();
                $data['flashsale_sold']  = $fsDetail?->sold ?? 0;
                $data['flashsale_qty']   = $fsDetail?->qty ?? 0;
                $data['flashsale_price'] = $fsDetail?->flashsale_price ?? 0;
                $data['is_flash_sale'] = true;
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

                if ($product->is_flash_sale) {
                    $fsDetails = $product->is_flash_sale->details()
                        ->where('product_id', $product->id)
                        ->whereNotNull('product_variation_id')
                        ->get();
                    $data['flashsale_price'] = $fsDetails->isNotEmpty() ? $fsDetails->min('flashsale_price') : 0;
                }
            } else {
                $data['price'] = 0;
                $data['promotion_price'] = 0;
            }
            return $data;
        });
    }
}
