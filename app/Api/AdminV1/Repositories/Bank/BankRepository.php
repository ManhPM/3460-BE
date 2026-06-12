<?php

namespace App\Api\AdminV1\Repositories\Bank;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Bank;

class BankRepository extends EloquentRepository implements BankRepositoryInterface
{
    public function getModel(): string
    {
        return Bank::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery();

        // Search
        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('bank_account', 'like', "%{$search}%")
                    ->orWhere('bank_account_number', 'like', "%{$search}%");
            });
        }

        // Column-specific filters
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        if (request()->has('code') && !empty(request('code'))) {
            $query->where('code', 'like', "%" . request('code') . "%");
        }

        if (request()->has('bank_account') && !empty(request('bank_account'))) {
            $query->where('bank_account', 'like', "%" . request('bank_account') . "%");
        }

        if (request()->has('bank_account_number') && !empty(request('bank_account_number'))) {
            $query->where('bank_account_number', 'like', "%" . request('bank_account_number') . "%");
        }

        if (request()->has('is_active') && request('is_active') !== '' && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        // Sort: is_active = 1 first, then by name
        return $query->orderBy('is_active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    public function getUniqueBanks()
    {
        return $this->model->newQuery()
            ->select('id', 'name', 'code', 'logo')
            ->groupBy('id', 'name', 'code', 'logo')
            ->orderBy('name', 'asc')
            ->get();
    }
}
