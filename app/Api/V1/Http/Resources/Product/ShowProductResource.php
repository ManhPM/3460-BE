<?php

namespace App\Api\V1\Http\Resources\Product;

use App\Api\V1\Http\Resources\Review\ReviewResource;
use App\Enums\Attribute\AttributeType;
use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Support\Carbon;
use App\Models\Admin;
use App\Models\AdminInventory;
use App\Models\Wishlist;

class ShowProductResource extends JsonResource
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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'categories' => implode(',', $this->categories->pluck('name')->toArray()),
            'is_contact_price' => $this->is_contact_price ?? 0,
            'total_sold' => $this->total_sold ?? 0,
            'is_flash_sale' => false,
            'avatar' => asset($this->avatar),
            'gallery' => ($this->gallery && count($this->gallery) > 0) ? array_map(function ($value) {
                return asset($value);
            }, $this->gallery->toArray()) : [asset($this->avatar)],
            'desc' => $this->desc,
            'avg_rating' => round($this->avg_rating, 1),
            'reviews' => $this->reviews ? new ReviewResource($this->reviews) : [],
            'review' => $this->getReviewStatistics(),
            'branches' => $this->getBranchesInventory(),
            'is_wishlist' => $this->checkIsWishlist(),
        ];

        if ($this->is_flash_sale) {
            $data['is_flash_sale'] = true;
            $data['end_time'] = Carbon::parse($this->is_flash_sale->end_time)->toISOString();

            $fsDetails = $this->is_flash_sale->details()
                ->where('product_id', $this->id)
                ->get();

            $data['flashsale_sold']  = (int) $fsDetails->sum('sold');
            $data['flashsale_qty']   = (int) $fsDetails->sum('qty');
            $data['flashsale_price'] = $fsDetails->isNotEmpty() ? (float) $fsDetails->min('flashsale_price') : 0;
        }

        if ($this->type == ProductType::Simple) {
            $data['price'] = $this->price ?? 0;
            $data['promotion_price'] = $this->promotion_price ?? 0;
        } elseif ($this->productAttributes) {
            $data = $this->handlePriceVariation($data);
            $data['attributes'] = $this->productAttributes->map(function ($productAttribute) {
                $attribute = $productAttribute->attribute;
                $variations = $productAttribute->attribute_variations->pluck('name')->toArray();

                return [
                    'name' => $attribute->name, // Lấy tên hiển thị của thuộc tính
                    'values' => $variations,    // Danh sách các giá trị
                ];
            })->toArray();

            $data['variants'] = $this->productVariations->map(function ($item) {
                return [
                    'id' => $item->id,
                    'price' => $item->price ?? 0,
                    'promotion_price' => $item->promotion_price ?? 0,
                    'flashsale_price' => $item->flashsale_price ?? 0,
                    'image' => asset($item->image),
                    'attributes' => $item->attribute_variations->map(function ($attrVar) {
                        return [
                            'attribute_id' => $attrVar->attribute->id,
                            'attribute_name' => $attrVar->attribute->name,
                            'variation_name' => $attrVar->name,
                            'meta_value' => $attrVar->meta_value
                        ];
                    })
                ];
            });
        }
        return $data;
    }

    private function handlePriceVariation($data)
    {
        if ($this->productVariations) {
            if ($this->productVariations->count() == 1) {
                $data['price'] = $this->productVariations[0]->price ?? 0;
                $data['promotion_price'] = $this->productVariations[0]->promotion_price ?? 0;
                if ($this->is_flash_sale) {
                    // 1 biến thể: lấy detail theo product_variation_id
                    $variationId = $this->productVariations[0]->id;
                    $fsDetail = $this->is_flash_sale->details()
                        ->where('product_id', $this->id)
                        ->where('product_variation_id', $variationId)
                        ->first();
                    $data['flashsale_sold']  = $fsDetail?->sold ?? 0;
                    $data['flashsale_qty']   = $fsDetail?->qty ?? 0;
                    $data['flashsale_price'] = $fsDetail?->flashsale_price ?? 0;
                }
            } elseif ($this->productVariations->count() > 1) {
                $prices = [];
                $promotion_prices = [];
                foreach ($this->productVariations as $variation) {
                    if (!is_null($variation->promotion_price)) {
                        $prices[] = $variation->promotion_price;
                        $promotion_prices[] = $variation->promotion_price;
                    } elseif (!is_null($variation->price)) {
                        $prices[] = $variation->price;
                        $promotion_prices[] = $variation->price;
                    }
                }

                $data['price'] = !empty($prices) ? min($prices) : 0;
                $data['promotion_price'] = !empty($promotion_prices) ? min($promotion_prices) : 0;

                if ($this->is_flash_sale) {
                    // Nhiều biến thể: lấy tất cả details của sản phẩm
                    $fsDetails = $this->is_flash_sale->details()
                        ->where('product_id', $this->id)
                        ->whereNotNull('product_variation_id')
                        ->get();

                    $data['flashsale_sold']  = $fsDetails->sum('sold');
                    $data['flashsale_qty']   = $fsDetails->sum('qty');
                    $data['flashsale_price'] = $fsDetails->isNotEmpty() ? $fsDetails->min('flashsale_price') : 0;
                }
            }
        } else {
            $data['price'] = 0;
            $data['promotion_price'] = 0;
        }
        return $data;
    }

    private function handleAttribute($productAttribute)
    {
        $attribute = $productAttribute->attribute;
        $attributesVariations = $productAttribute->attribute_variations;
        $productAttribute = [];

        $productAttribute = [
            'id' => $attribute->id,
            'type' => $attribute->type,
            'name' => $attribute->name
        ];
        $productAttribute['variations'] = $attributesVariations->map(function ($attributesVariation) use ($productAttribute) {
            return [
                'id' => $attributesVariation->id,
                'name' => $attributesVariation->name,
                'meta_value' => $productAttribute['type'] == AttributeType::Color ? $attributesVariation->meta_value : null
            ];
        });

        return $productAttribute;
    }

    private function getReviewStatistics()
    {
        $reviews = $this->reviews ?? collect();

        $fiveStarCount = $reviews->where('rating', 5)->count();
        $fourStarCount = $reviews->where('rating', 4)->count();
        $threeStarCount = $reviews->where('rating', 3)->count();
        $twoStarCount = $reviews->where('rating', 2)->count();
        $oneStarCount = $reviews->where('rating', 1)->count();
        $totalCount = $reviews->count();

        return [
            'five_star_count' => $fiveStarCount,
            'four_star_count' => $fourStarCount,
            'three_star_count' => $threeStarCount,
            'two_star_count' => $twoStarCount,
            'one_star_count' => $oneStarCount,
            'total_count' => $totalCount,
        ];
    }

    private function getBranchesInventory()
    {
        // Lấy tất cả chi nhánh (Admin có role 'branch')
        $branches = Admin::query()
            ->select(['id', 'branch_name', 'branch_phone', 'branch_address'])
            ->whereHas('roles', function ($q) {
                $q->where('name', 'branch');
            })
            ->get();

        $productId = $this->id;
        $availableBranches = collect();

        // Nếu sản phẩm có biến thể
        if ($this->type == ProductType::Variable && $this->productVariations && $this->productVariations->count() > 0) {
            foreach ($branches as $branch) {
                $variations = [];
                $hasStock = false;

                // Lấy tồn kho cho từng biến thể
                foreach ($this->productVariations as $variation) {
                    $inventory = AdminInventory::where('admin_id', $branch->id)
                        ->where('product_id', $productId)
                        ->where('product_variation_id', $variation->id)
                        ->first();

                    $qty = $inventory ? $inventory->qty : 0;

                    // Chỉ thêm biến thể có tồn kho > 0
                    if ($qty > 0) {
                        $variations[] = [
                            'variation_id' => $variation->id,
                            'qty' => $qty
                        ];
                        $hasStock = true;
                    }
                }

                // Chỉ thêm chi nhánh nếu có ít nhất một biến thể còn hàng
                if ($hasStock && count($variations) > 0) {
                    $availableBranches->push([
                        'id' => $branch->id,
                        'branch_name' => $branch->branch_name,
                        'branch_phone' => $branch->branch_phone,
                        'branch_address' => $branch->branch_address,
                        'variations' => $variations
                    ]);
                }
            }
        } else {
            // Sản phẩm đơn giản
            foreach ($branches as $branch) {
                $inventory = AdminInventory::where('admin_id', $branch->id)
                    ->where('product_id', $productId)
                    ->whereNull('product_variation_id')
                    ->first();

                $qty = $inventory ? $inventory->qty : 0;

                // Chỉ thêm chi nhánh nếu còn hàng
                if ($qty > 0) {
                    $availableBranches->push([
                        'id' => $branch->id,
                        'branch_name' => $branch->branch_name,
                        'branch_phone' => $branch->branch_phone,
                        'branch_address' => $branch->branch_address,
                        'qty' => $qty
                    ]);
                }
            }
        }

        return $availableBranches;
    }

    private function checkIsWishlist()
    {
        $user = auth('user')->user();

        if (!$user) {
            return 0;
        }

        $wishlist = Wishlist::where('product_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($wishlist) {
            return 1;
        }

        return 0;
    }
}
