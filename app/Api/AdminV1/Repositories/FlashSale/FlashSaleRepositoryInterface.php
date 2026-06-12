<?php

namespace App\Api\AdminV1\Repositories\FlashSale;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface FlashSaleRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

