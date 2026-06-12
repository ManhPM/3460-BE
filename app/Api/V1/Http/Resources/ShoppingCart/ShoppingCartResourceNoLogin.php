<?php

namespace App\Api\V1\Http\Resources\ShoppingCart;

use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Support\AuthSupport;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Admin;

class ShoppingCartResourceNoLogin extends ResourceCollection
{
    use AuthSupport;
    public function toArray($request)
    {
        $grouped = $this->collection->groupBy('admin_id');

        return $grouped->map(function ($items, $adminId) {
            $admin = $adminId ? Admin::find($adminId) : null;
            $branch = [
                'admin_id' => (int) $adminId,
                'branch_name' => $admin->branch_name ?? null,
                'branch_phone' => $admin->phone ?? null,
            ];

            $details = collect($items)->map(function ($shoppingCart) {
                $product = Product::find($shoppingCart['product_id']);
                if ($shoppingCart['variation_id']) {
                    $productVariation = ProductVariation::find($shoppingCart['variation_id']);
                }
                $data = [
                    'id' => $shoppingCart['id'],
                    'qty' => (int) $shoppingCart['qty'],
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'avatar' => asset($product->avatar)
                    ],
                ];
                if ($product->is_flash_sale) {
                    $data['product']['flashsale_price'] = $product->flashsale_price;
                    $data['product']['is_flash_sale'] = true;
                }
                if ($product->type == ProductType::Simple) {
                    $data['product']['min_price'] = $product->price;
                    $data['product']['min_promotion_price'] = $product->promotion_price;
                }
                if (isset($productVariation)) {
                    $item = $productVariation;
                    $name = $product->name;
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
                        'flashsale_price' => $item->flashsale_price,
                        'avatar' => asset($item->avatar),
                    ]);
                }
                return $data;
            })->values();

            return array_merge($branch, ['items' => $details]);
        })->values();
    }
}
