<?php

namespace App\Api\AdminV1\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'promotion_price' => $this->promotion_price,
            'qty' => $this->qty,
            'type' => $this->type?->value ?? $this->type,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured?->value ?? $this->is_featured,
            'is_contact_price' => $this->is_contact_price,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'gallery' => $this->gallery ? array_map(function ($img) {
                return asset($img);
            }, (array) $this->gallery) : [],
            'desc' => $this->desc,
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                });
            }),
            'productAttributes' => $this->whenLoaded('productAttributes', function () {
                return $this->productAttributes->map(function ($productAttribute) {
                    $attributeData = null;
                    if ($productAttribute->relationLoaded('attribute')) {
                        $attributeData = [
                            'id' => $productAttribute->attribute->id,
                            'name' => $productAttribute->attribute->name,
                            'type' => $productAttribute->attribute->type?->value ?? $productAttribute->attribute->type,
                        ];
                    }
                    $attributeVariations = [];
                    if ($productAttribute->relationLoaded('attribute_variations')) {
                        $attributeVariations = $productAttribute->attribute_variations->map(function ($variation) {
                            return [
                                'id' => $variation->id,
                                'name' => $variation->name,
                                'meta_value' => $variation->meta_value,
                                'position' => $variation->position,
                            ];
                        })->toArray();
                    }
                    return [
                        'id' => $productAttribute->id,
                        'attribute_id' => $productAttribute->attribute_id,
                        'position' => $productAttribute->position,
                        'attribute' => $attributeData,
                        'attributeVariations' => $attributeVariations,
                    ];
                });
            }),
            'productVariations' => $this->whenLoaded('productVariations', function () {
                return $this->productVariations->map(function ($productVariation) {
                    $attributeVariations = [];
                    // Check both relation names
                    $loadedVariations = null;
                    if ($productVariation->relationLoaded('attribute_variations')) {
                        $loadedVariations = $productVariation->attribute_variations;
                    } elseif ($productVariation->relationLoaded('attributeVariations')) {
                        $loadedVariations = $productVariation->attributeVariations;
                    } elseif (isset($productVariation->attribute_variations)) {
                        $loadedVariations = $productVariation->attribute_variations;
                    } elseif (isset($productVariation->attributeVariations)) {
                        $loadedVariations = $productVariation->attributeVariations;
                    }

                    if ($loadedVariations) {
                        $attributeVariations = $loadedVariations->map(function ($variation) {
                            $attributeData = null;
                            if ($variation->relationLoaded('attribute')) {
                                $attributeData = [
                                    'id' => $variation->attribute->id,
                                    'name' => $variation->attribute->name,
                                    'type' => $variation->attribute->type?->value ?? $variation->attribute->type,
                                ];
                            } elseif (isset($variation->attribute)) {
                                $attributeData = [
                                    'id' => $variation->attribute->id,
                                    'name' => $variation->attribute->name,
                                    'type' => $variation->attribute->type?->value ?? $variation->attribute->type,
                                ];
                            }
                            return [
                                'id' => $variation->id,
                                'name' => $variation->name,
                                'meta_value' => $variation->meta_value,
                                'attribute' => $attributeData,
                            ];
                        })->toArray();
                    }
                    return [
                        'id' => $productVariation->id,
                        'sku' => $productVariation->sku,
                        'price' => $productVariation->price,
                        'promotion_price' => $productVariation->promotion_price,
                        'qty' => $productVariation->qty,
                        'position' => $productVariation->position,
                        'image' => $productVariation->image ? asset($productVariation->image) : null,
                        'attributeVariations' => $attributeVariations,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
