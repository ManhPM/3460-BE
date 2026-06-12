<?php

namespace App\Api\AdminV1\Repositories\WalletTransaction;

use App\Admin\Repositories\EloquentRepository;
use App\Models\WalletTransaction;

class WalletTransactionRepository extends EloquentRepository implements WalletTransactionRepositoryInterface
{
    public function getModel(): string
    {
        return WalletTransaction::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('user');

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        // Search user by name or email
        if (request()->has('user_id') && !empty(request('user_id'))) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('fullname', 'like', "%" . request('user_id') . "%")
                    ->orWhere('email', 'like', "%" . request('user_id') . "%");
            });
        }

        if (request()->has('amount') && !empty(request('amount'))) {
            $query->where('amount', 'like', "%" . request('amount') . "%");
        }

        // Select/Dropdown - Exact match
        if (request()->has('type') && request('type') !== '' && request('type') !== null) {
            $query->where('type', request('type'));
        }

        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        if (request()->has('note') && !empty(request('note'))) {
            $query->where('note', 'like', "%" . request('note') . "%");
        }

        if (request()->has('order_id') && !empty(request('order_id'))) {
            $query->where('order_id', 'like', "%" . request('order_id') . "%");
        }

        // Date/Datetime - Dùng like
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}

