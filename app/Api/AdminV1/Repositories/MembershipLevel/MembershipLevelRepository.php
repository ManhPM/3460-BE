<?php

namespace App\Api\AdminV1\Repositories\MembershipLevel;

use App\Admin\Repositories\EloquentRepository;
use App\Models\MembershipLevel;

class MembershipLevelRepository extends EloquentRepository implements MembershipLevelRepositoryInterface
{
    public function getModel(): string
    {
        return MembershipLevel::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->withCount('users');

        // Search
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        if (request()->has('min_points') && !empty(request('min_points'))) {
            $query->where('min_points', 'like', "%" . request('min_points') . "%");
        }

        if (request()->has('discount_percentage') && !empty(request('discount_percentage'))) {
            $query->where('discount_percentage', 'like', "%" . request('discount_percentage') . "%");
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('min_points', 'asc')->paginate($perPage);
    }
}
