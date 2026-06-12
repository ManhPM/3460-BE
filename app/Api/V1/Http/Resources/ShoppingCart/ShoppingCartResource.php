<?php

namespace App\Api\V1\Http\Resources\ShoppingCart;

use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Support\AuthSupport;

class ShoppingCartResource extends ResourceCollection
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
        $grouped = $this->collection->groupBy('admin_id');

        return $grouped->map(function ($items, $adminId) {
            $first = $items->first();
            $admin = $first->admin;
            $branch = [
                'admin_id' => (int) $adminId,
                'branch_name' => $admin->branch_name ?? null,
                'branch_phone' => $admin->phone ?? null,
            ];

            $details = $items->map(function ($shoppingCart) {
                $data = [
                    'id' => $shoppingCart->id,
                    'qty' => $shoppingCart->qty,
                    'product' => [
                        'id' => $shoppingCart->product->id,
                        'name' => $shoppingCart->product->name,
                        'avatar' => asset($shoppingCart->product->avatar)
                    ]
                ];

                $flashSale = $shoppingCart->product->flash_sales()->first();
                $isFlashSale = $flashSale ? true : false;

                if ($isFlashSale) {
                    $data['product']['is_flash_sale'] = true;
                } else {
                    $data['product']['is_flash_sale'] = false;
                }

                if ($shoppingCart->product->type == ProductType::Simple) {
                    $data['product']['min_price'] = $shoppingCart->product->price;
                    $data['product']['min_promotion_price'] = $shoppingCart->product->promotion_price;
                    if ($isFlashSale) {
                        $data['product']['flashsale_price'] = $flashSale->pivot->flashsale_price;
                    }
                }

                if ($shoppingCart->productVariation) {
                    $item = $shoppingCart->productVariation;
                    $name = $shoppingCart->product->name;
                    $attributes = '';
                    $item->attribute_variations->each(function ($attr, $index) use (&$attributes) {
                        if ($index != 0) {
                            $attributes .= ', ' . $attr->name;
                        } else {
                            $attributes .= $attr->name;
                        }
                    });
                    $data['product_variation'] = array_merge([
                        'id' => $item->id,
                        'attributes' => $attributes,
                        'min_price' => $item->price,
                        'min_promotion_price' => $item->promotion_price,
                        'avatar' => asset($item->image),
                    ]);
                    if ($isFlashSale) {
                        $data['product_variation']['flashsale_price'] = $item->flashsale_price;
                    }
                }
                return $data;
            })->values();

            return array_merge($branch, ['items' => $details]);
        })->values();
    }
}
