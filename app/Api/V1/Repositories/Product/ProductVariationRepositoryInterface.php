<?php

namespace App\Api\V1\Repositories\Product;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ProductVariationRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByProductAndAttributeVariation($product_id, array $variation_id = []);
}
