<?php

namespace App\Api\AdminV1\Repositories\Inventory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\AdminInventory;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

class InventoryRepository extends EloquentRepository implements InventoryRepositoryInterface
{
    public function getModel(): string
    {
        return AdminInventory::class;
    }

    public function getFiltered()
    {
        $adminId = request('admin_id');
        $search = request('search', '');

        if (!$adminId) {
            return collect([])->paginate(15);
        }

        $query = Product::query()
            ->select(['id', 'name', 'avatar', 'price', 'promotion_price', 'type'])
            ->with(['productVariations' => function ($q) {
                $q->select(['id', 'product_id', 'price', 'promotion_price', 'image', 'position']);
                $q->with(['attributeVariations' => function ($q2) {
                    $q2->select(['attributes_variations.id', 'attributes_variations.name']);
                }]);
                $q->orderBy('position', 'asc');
            }]);

        // Search by product name
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Preload inventory data
        $products = $query->orderBy('id', 'desc')->paginate(request('per_page', 20));

        $productIds = $products->pluck('id')->all();
        $variationIds = collect($products->pluck('productVariations')->flatten())->pluck('id')->all();

        // Get all inventory rows for this admin
        $inventoryRows = AdminInventory::query()
            ->where('admin_id', $adminId)
            ->where(function ($q) use ($productIds, $variationIds) {
                $q->whereIn('product_id', $productIds)
                    ->orWhereIn('product_variation_id', $variationIds);
            })
            ->get()
            ->groupBy(function ($row) {
                return ($row->product_id ?: 0) . ':' . ($row->product_variation_id ?: 0);
            });

        // Attach inventory data to products
        foreach ($products as $product) {
            $simpleKey = $product->id . ':0';
            $simpleQty = optional($inventoryRows->get($simpleKey)[0] ?? null)->qty ?? 0;
            $product->inventory_qty = $simpleQty;

            foreach ($product->productVariations as $variation) {
                $key = $product->id . ':' . $variation->id;
                $qty = optional($inventoryRows->get($key)[0] ?? null)->qty ?? 0;
                $variation->inventory_qty = $qty;
            }
        }

        return $products;
    }

    public function updateQuantity(int $adminId, ?int $productId, ?int $productVariationId, int $qty)
    {
        if (!$productId && !$productVariationId) {
            return false;
        }

        if ($productVariationId) {
            $variation = ProductVariation::find($productVariationId);
            if (!$variation) {
                return false;
            }
            $productId = $variation->product_id;
        } else {
            $product = Product::find($productId);
            if (!$product) {
                return false;
            }
        }

        return AdminInventory::updateOrCreate(
            [
                'admin_id' => $adminId,
                'product_id' => $productId,
                'product_variation_id' => $productVariationId,
            ],
            [
                'qty' => $qty,
            ]
        );
    }
}
