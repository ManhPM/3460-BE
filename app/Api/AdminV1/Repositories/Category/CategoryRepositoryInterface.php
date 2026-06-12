<?php

namespace App\Api\AdminV1\Repositories\Category;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface CategoryRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function hasProducts(int $id): bool;
}
