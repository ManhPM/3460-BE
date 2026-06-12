<?php

namespace App\Api\AdminV1\Repositories\Inventory;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface InventoryRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function updateQuantity(int $adminId, ?int $productId, ?int $productVariationId, int $qty);
}
