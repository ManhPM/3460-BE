<?php

namespace App\Api\AdminV1\Repositories\ShippingRate;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ShippingRateRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

