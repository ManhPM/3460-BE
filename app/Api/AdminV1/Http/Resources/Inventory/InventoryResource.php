<?php

namespace App\Api\AdminV1\Http\Resources\Inventory;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $product = $this->resource;
        $hasVariations = $product->productVariations && $product->productVariations->count() > 0;

        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'avatar' => $product->avatar ? asset($product->avatar) : null,
            'price' => $product->price,
            'promotion_price' => $product->promotion_price,
            'type' => $product->type?->value ?? $product->type,
            'type_name' => $product->type?->description ?? '',
            'has_variations' => $hasVariations,
            'inventory_qty' => $product->inventory_qty ?? 0,
        ];

        if ($hasVariations) {
            $data['variations'] = $product->productVariations->map(function ($variation) {
                $optionNames = $variation->attributeVariations->pluck('name')->implode(' / ');
                $img = $variation->image ?: $product->avatar;
                $price = $variation->promotion_price ?? $variation->price;
                $rootPrice = $variation->price;

                if ($price === null) {
                    $price = $product->promotion_price ?? $product->price;
                    $rootPrice = $product->price;
                }

                return [
                    'id' => $variation->id,
                    'product_id' => $variation->product_id,
                    'image' => $img ? asset($img) : null,
                    'option_names' => $optionNames ?: 'Phân loại',
                    'price' => $price,
                    'root_price' => $rootPrice,
                    'promotion_price' => $variation->promotion_price,
                    'has_promotion' => $variation->promotion_price && $variation->promotion_price < $variation->price,
                    'inventory_qty' => $variation->inventory_qty ?? 0,
                ];
            });
        }

        return $data;
    }
}
