<?php

namespace App\Api\AdminV1\Repositories\Order;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface OrderRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function findOrFailWithRelations(int $id);
}
