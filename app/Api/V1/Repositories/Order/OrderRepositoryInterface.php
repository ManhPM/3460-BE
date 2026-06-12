<?php

namespace App\Api\V1\Repositories\Order;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface OrderRepositoryInterface extends EloquentRepositoryInterface
{
    public function getByKeyAuthCurrent($filter);
    public function getAffiliateOrders($filter);
}
