<?php

namespace App\Api\AdminV1\Repositories\CommissionWithdrawal;

use App\Admin\Repositories\EloquentRepository;
use App\Models\CommissionWithdrawal;

class CommissionWithdrawalRepository extends EloquentRepository implements CommissionWithdrawalRepositoryInterface
{
    public function getModel(): string
    {
        return CommissionWithdrawal::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('user');

        // Search
        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by user
        if (request()->has('user_id') && !empty(request('user_id'))) {
            $query->where('user_id', request('user_id'));
        }

        // Filter by status
        if (request()->has('status') && !empty(request('status'))) {
            $query->where('status', request('status'));
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
