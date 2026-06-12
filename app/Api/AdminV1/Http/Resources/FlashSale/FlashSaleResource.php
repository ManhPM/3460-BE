<?php

namespace App\Api\AdminV1\Http\Resources\FlashSale;

use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_active' => $this->is_active,
            'details' => $this->whenLoaded('details', function () {
                return $this->details->map(function ($detail) {
                    $product = null;
                    if ($detail->relationLoaded('product') && $detail->product) {
                        $product = [
                            'id' => $detail->product->id,
                            'name' => $detail->product->name,
                            'slug' => $detail->product->slug,
                            'avatar' => $detail->product->avatar ? asset($detail->product->avatar) : null,
                            'type' => $detail->product->type,
                            'type_name' => $detail->product->type?->description ?? '',
                        ];
                    }

                    $productVariation = null;
                    if ($detail->relationLoaded('product_variation') && $detail->product_variation) {
                        $attributeVariations = [];
                        if ($detail->product_variation->relationLoaded('attributeVariations')) {
                            $attributeVariations = $detail->product_variation->attributeVariations->map(function ($attrVar) {
                                return [
                                    'id' => $attrVar->id,
                                    'name' => $attrVar->name,
                                ];
                            })->toArray();
                        }

                        $productVariation = [
                            'id' => $detail->product_variation->id,
                            'image' => $detail->product_variation->image ? asset($detail->product_variation->image) : null,
                            'option_names' => $detail->product_variation->option_names ?? '',
                            'attribute_variations' => $attributeVariations,
                        ];
                    }

                    return [
                        'id' => $detail->id,
                        'flash_sale_id' => $detail->flash_sale_id,
                        'product_id' => $detail->product_id,
                        'product_variation_id' => $detail->product_variation_id,
                        'qty' => $detail->qty,
                        'sold' => $detail->sold ?? 0,
                        'flashsale_price' => $detail->flashsale_price,
                        'product' => $product,
                        'product_variation' => $productVariation,
                    ];
                });
            }),
        ];
    }
}

