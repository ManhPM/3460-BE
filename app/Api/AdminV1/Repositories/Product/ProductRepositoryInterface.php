<?php

namespace App\Api\AdminV1\Repositories\Product;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ProductRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function findOrFailWithRelations($id);
    public function duplicate(int $id);
}
