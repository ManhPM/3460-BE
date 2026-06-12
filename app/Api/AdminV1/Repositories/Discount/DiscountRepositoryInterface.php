<?php

namespace App\Api\AdminV1\Repositories\Discount;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface DiscountRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}
