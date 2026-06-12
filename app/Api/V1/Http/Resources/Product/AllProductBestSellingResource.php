<?php

namespace App\Api\V1\Http\Resources\Product;

use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Support\AuthSupport;

class AllProductBestSellingResource extends ResourceCollection
{
    use AuthSupport;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
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
                'avatar' => asset($product->avatar),
                'total_sold' => $product->total_sold,
                'avg_rating' => round($product->avg_rating, 1),
                'variant_name' => $variantName,
            ];
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
