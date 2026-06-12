<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get filtered and paginated users
     */
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by membership level
        if (!empty($filters['membership_level_id'])) {
            $query->where('membership_level_id', $filters['membership_level_id']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Get user with orders
     */
    public function getUserOrders(int $userId, int $perPage = 10)
    {
        $user = $this->findOrFail($userId);
        return $user->orders()->with('items.product')->paginate($perPage);
    }

    /**
     * Get user addresses
     */
    public function getUserAddresses(int $userId)
    {
        $user = $this->findOrFail($userId);
        return $user->addresses;
    }
}
