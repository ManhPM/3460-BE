<?php

namespace App\Admin\Repositories\Order;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface OrderDetailRepositoryInterface extends EloquentRepositoryInterface
{
    public function getTotalEarningAffiliate($code);
    public function getAffiliate($code);
}
