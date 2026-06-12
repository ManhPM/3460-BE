<?php

namespace App\Api\AdminV1\Repositories\Order;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderRepository extends EloquentRepository implements OrderRepositoryInterface
{
    public function getModel(): string
    {
        return Order::class;
    }

    public function getFiltered()
    {
        // Only load user and admin for list view to reduce query load
        $query = $this->model->with(['user', 'admin'])->where('is_deleted', 0);
        if (!isSuperAdmin()) {
            $query->where('admin_id', auth()->user()->id);
        }

        // Search by code, user name, email, phone
        if (request()->has('code') && !empty(request('code'))) {
            $query->where('code', 'like', "%" . request('code') . "%");
        }

        if (request()->has('fullname') && !empty(request('fullname'))) {
            $query->where('fullname', 'like', "%" . request('fullname') . "%");
        }

        if (request()->has('email') && !empty(request('email'))) {
            $query->where('email', 'like', "%" . request('email') . "%");
        }

        if (request()->has('phone') && !empty(request('phone'))) {
            $query->where('phone', 'like', "%" . request('phone') . "%");
        }

        if (request()->has('user_id') && !empty(request('user_id'))) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('fullname', 'like', "%" . request('user_id') . "%")
                    ->orWhere('email', 'like', "%" . request('user_id') . "%");
            });
        }

        // Status filter
        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        // Payment status filter
        if (request()->has('payment_status') && request('payment_status') !== '' && request('payment_status') !== null) {
            $query->where('payment_status', request('payment_status'));
        }

        // Payment method filter
        if (request()->has('payment_method') && request('payment_method') !== '' && request('payment_method') !== null) {
            $query->where('payment_method', request('payment_method'));
        }

        // Admin/Branch filter
        if (request()->has('admin_id') && request('admin_id') !== '' && request('admin_id') !== null) {
            $query->where('admin_id', request('admin_id'));
        }

        // Date filters
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('from_date') && !empty(request('from_date'))) {
            $query->whereDate('created_at', '>=', request('from_date'));
        }

        if (request()->has('to_date') && !empty(request('to_date'))) {
            $query->whereDate('created_at', '<=', request('to_date'));
        }

        // Sort
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = request('per_page', 15);
        return $query->paginate($perPage);
    }



    public function findOrFailWithRelations(int $id)
    {
        return $this->model->with([
            'user',
            'admin',
            'province',
            'ward',
            'details.product',
            'details.productVariation.attributeVariations'
        ])->findOrFail($id);
    }
}
