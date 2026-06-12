<?php

namespace App\Api\V1\Repositories\Product;

use App\Admin\Repositories\Product\ProductVariationRepository as AdminProductVariationRepository;
use App\Api\V1\Repositories\Product\ProductVariationRepositoryInterface;

class ProductVariationRepository extends AdminProductVariationRepository implements ProductVariationRepositoryInterface
{
    public function findByProductAndAttributeVariation($product_id, array $variation_id = [])
    {
        $this->instance = $this->model->where('product_id', $product_id)
            ->whereHas('attribute_variations', function ($query) use ($variation_id) {
                $query->whereIn('id', $variation_id);
            }, '=', count($variation_id))
            ->firstOrFail();
        return $this->instance;
    }
}
