<?php

namespace App\Admin\Repositories\ShoppingCart;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ShoppingCartRepositoryInterface extends EloquentRepositoryInterface
{
    public function findManyById(array $ids);
}
