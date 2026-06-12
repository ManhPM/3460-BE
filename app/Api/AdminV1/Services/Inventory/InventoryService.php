<?php

namespace App\Api\AdminV1\Services\Inventory;

use App\Api\AdminV1\Repositories\Inventory\InventoryRepositoryInterface;

class InventoryService
{
    protected $repository;

    public function __construct(InventoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function updateQuantity(int $adminId, ?int $productId, ?int $productVariationId, int $qty)
    {
        return $this->repository->updateQuantity($adminId, $productId, $productVariationId, $qty);
    }
}

