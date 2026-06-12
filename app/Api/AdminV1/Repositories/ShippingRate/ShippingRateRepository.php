<?php

namespace App\Api\AdminV1\Repositories\ShippingRate;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ShippingRate;

class ShippingRateRepository extends EloquentRepository implements ShippingRateRepositoryInterface
{
    public function getModel(): string
    {
        return ShippingRate::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with(['province', 'ward']);

        // Filter by province
        if (request()->has('province') && !empty(request('province'))) {
            $query->whereHas('province', fn($q) => $q->where('name', 'like', '%' . request('province') . '%'));
        }

        // Filter by ward
        if (request()->has('ward') && !empty(request('ward'))) {
            $query->whereHas('ward', fn($q) => $q->where('name', 'like', '%' . request('ward') . '%'));
        }

        // Filter by price
        if (request()->has('price') && !empty(request('price'))) {
            $query->where('price', 'like', '%' . request('price') . '%');
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('province_id', 'asc')->paginate($perPage);
    }
}
