<?php

namespace App\Api\AdminV1\Repositories\FlashSale;

use App\Admin\Repositories\EloquentRepository;
use App\Models\FlashSale;

class FlashSaleRepository extends EloquentRepository implements FlashSaleRepositoryInterface
{
    public function getModel(): string
    {
        return FlashSale::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('details');

        // Column-specific filters
        // Name filter
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        // Start time filter (datetime)
        if (request()->has('start_time') && !empty(request('start_time'))) {
            $query->where('start_time', 'like', '%' . request('start_time') . '%');
        }

        // End time filter (datetime)
        if (request()->has('end_time') && !empty(request('end_time'))) {
            $query->where('end_time', 'like', '%' . request('end_time') . '%');
        }

        // Is active filter (select - exact match)
        if (request()->has('is_active') && request('is_active') !== '' && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }

        // Global search (fallback)
        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('start_time', 'desc')->paginate($perPage);
    }
}

