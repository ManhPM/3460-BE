<?php

namespace App\Admin\Repositories\ShippingRate;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ShippingRate;

class ShippingRateRepository extends EloquentRepository implements ShippingRateRepositoryInterface
{
    public function getModel(): string
    {
        return ShippingRate::class;
    }
}
