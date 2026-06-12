<?php

namespace App\Api\AdminV1\Repositories\Discount;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Discount;

class DiscountRepository extends EloquentRepository implements DiscountRepositoryInterface
{
    public function getModel(): string
    {
        return Discount::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery();

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('code') && !empty(request('code'))) {
            $query->where('code', 'like', "%" . request('code') . "%");
        }

        if (request()->has('type') && request('type') !== '' && request('type') !== null) {
            $query->where('type', request('type'));
        }

        if (request()->has('min_order_amount') && !empty(request('min_order_amount'))) {
            $query->where('min_order_amount', 'like', "%" . request('min_order_amount') . "%");
        }

        if (request()->has('discount_value') && !empty(request('discount_value'))) {
            $query->where('discount_value', 'like', "%" . request('discount_value') . "%");
        }

        if (request()->has('max_discount_value') && !empty(request('max_discount_value'))) {
            $query->where('max_discount_value', 'like', "%" . request('max_discount_value') . "%");
        }

        if (request()->has('max_usage') && !empty(request('max_usage'))) {
            $query->where('max_usage', 'like', "%" . request('max_usage') . "%");
        }

        if (request()->has('max_usage_per_user') && !empty(request('max_usage_per_user'))) {
            $query->where('max_usage_per_user', 'like', "%" . request('max_usage_per_user') . "%");
        }

        if (request()->has('date_start') && !empty(request('date_start'))) {
            $query->where('date_start', 'like', "%" . request('date_start') . "%");
        }

        if (request()->has('date_end') && !empty(request('date_end'))) {
            $query->where('date_end', 'like', "%" . request('date_end') . "%");
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
