<?php

namespace App\Api\V1\Repositories\Order;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface OrderDetailRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAffiliate($code);
}
