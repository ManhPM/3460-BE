<?php

namespace App\Api\AdminV1\Repositories\VoucherProgram;

use App\Admin\Repositories\EloquentRepository;
use App\Models\VoucherProgram;

class VoucherProgramRepository extends EloquentRepository implements VoucherProgramRepositoryInterface
{
    public function getModel(): string
    {
        return VoucherProgram::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery();

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        if (request()->has('type') && request('type') !== '' && request('type') !== null) {
            $query->where('type', request('type'));
        }

        if (request()->has('voucher_type') && request('voucher_type') !== '' && request('voucher_type') !== null) {
            $query->where('voucher_type', request('voucher_type'));
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

        if (request()->has('qty') && !empty(request('qty'))) {
            $query->where('qty', 'like', "%" . request('qty') . "%");
        }

        if (request()->has('expiration_days') && !empty(request('expiration_days'))) {
            $query->where('expiration_days', 'like', "%" . request('expiration_days') . "%");
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
        $voucherProgram = $this->model->findOrFail($id);
        $newStatus = $voucherProgram->status === 1 ? 0 : 1;
        $voucherProgram->update(['status' => $newStatus]);
        return $voucherProgram;
    }
}

