<?php

namespace App\Api\AdminV1\Repositories\Voucher;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Voucher;

class VoucherRepository extends EloquentRepository implements VoucherRepositoryInterface
{
    public function getModel(): string
    {
        return Voucher::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('user');

        // Column-specific filters (theo key trong columns config)
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('code') && !empty(request('code'))) {
            $query->where('code', 'like', "%" . request('code') . "%");
        }

        if (request()->has('user_id') && !empty(request('user_id'))) {
            // Search by user fullname or email (user_id column search is for searching user fullname/email)
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('fullname', 'like', "%" . request('user_id') . "%")
                    ->orWhere('email', 'like', "%" . request('user_id') . "%");
            });
        }

        if (request()->has('date_end') && !empty(request('date_end'))) {
            $query->where('date_end', 'like', "%" . request('date_end') . "%");
        }

        if (request()->has('is_used') && request('is_used') !== '' && request('is_used') !== null) {
            $query->where('is_used', request('is_used'));
        }

        if (request()->has('min_order_amount') && !empty(request('min_order_amount'))) {
            $query->where('min_order_amount', 'like', "%" . request('min_order_amount') . "%");
        }

        if (request()->has('type') && request('type') !== '' && request('type') !== null) {
            $query->where('type', request('type'));
        }

        if (request()->has('voucher_type') && request('voucher_type') !== '' && request('voucher_type') !== null) {
            $query->where('voucher_type', request('voucher_type'));
        }

        if (request()->has('discount_value') && !empty(request('discount_value'))) {
            $query->where('discount_value', 'like', "%" . request('discount_value') . "%");
        }

        if (request()->has('max_discount_value') && !empty(request('max_discount_value'))) {
            $query->where('max_discount_value', 'like', "%" . request('max_discount_value') . "%");
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }


    public function toggleStatus(int $id)
    {
        $voucher = $this->model->findOrFail($id);
        $newStatus = $voucher->status === 'active' ? 'inactive' : 'active';
        $voucher->update(['status' => $newStatus]);
        return $voucher;
    }
}
