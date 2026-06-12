<?php

namespace App\Api\AdminV1\Repositories\User;

use App\Admin\Repositories\EloquentRepository;
use App\Models\User;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function getModel(): string
    {
        return User::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with(['member']);

        // Column-specific filters
        if (request()->has('fullname') && !empty(request('fullname'))) {
            $query->where('fullname', 'like', '%' . request('fullname') . '%');
        }

        if (request()->has('email') && !empty(request('email'))) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }

        if (request()->has('phone') && !empty(request('phone'))) {
            $query->where('phone', 'like', '%' . request('phone') . '%');
        }

        // Select/Dropdown - Exact match
        if (request()->has('is_email_verified') && request('is_email_verified') !== '' && request('is_email_verified') !== null) {
            $query->where('is_email_verified', request('is_email_verified'));
        }

        if (request()->has('is_phone_verified') && request('is_phone_verified') !== '' && request('is_phone_verified') !== null) {
            $query->where('is_phone_verified', request('is_phone_verified'));
        }

        // Filter by membership level
        if (request()->has('membership_id') && request('membership_id') !== '' && request('membership_id') !== null) {
            $membershipId = request('membership_id');
            // If it's numeric, search by ID, otherwise search by name
            if (is_numeric($membershipId)) {
                $query->where('membership_id', $membershipId);
            } else {
                $query->whereHas('member', function ($q) use ($membershipId) {
                    $q->where('name', 'like', '%' . $membershipId . '%');
                });
            }
        }

        // Date/Datetime - Dùng like
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', '%' . request('created_at') . '%');
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', '%' . request('updated_at') . '%');
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function search($searchTerm = null)
    {
        $query = $this->model->newQuery()->with(['member']);

        // Search term - tìm kiếm trong fullname, email, phone với OR conditions
        if ($searchTerm && !empty(trim($searchTerm))) {
            $searchTerm = trim($searchTerm);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('fullname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // Select/Dropdown - Exact match (vẫn giữ AND cho các filter khác)
        if (request()->has('is_email_verified') && request('is_email_verified') !== '' && request('is_email_verified') !== null) {
            $query->where('is_email_verified', request('is_email_verified'));
        }

        if (request()->has('is_phone_verified') && request('is_phone_verified') !== '' && request('is_phone_verified') !== null) {
            $query->where('is_phone_verified', request('is_phone_verified'));
        }

        // Filter by membership level
        if (request()->has('membership_id') && request('membership_id') !== '' && request('membership_id') !== null) {
            $membershipId = request('membership_id');
            if (is_numeric($membershipId)) {
                $query->where('membership_id', $membershipId);
            } else {
                $query->whereHas('member', function ($q) use ($membershipId) {
                    $q->where('name', 'like', '%' . $membershipId . '%');
                });
            }
        }

        // Date/Datetime filters
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', '%' . request('created_at') . '%');
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', '%' . request('updated_at') . '%');
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findOrFailWithRelations($id)
    {
        return $this->model->with(['member'])->findOrFail($id);
    }

    public function getUserOrders(int $userId, int $perPage = 10)
    {
        $user = $this->model->findOrFail($userId);
        return $user->orders()->with('items.product')->paginate($perPage);
    }

    public function getUserAddresses(int $userId)
    {
        $user = $this->model->findOrFail($userId);
        return $user->addresses;
    }
}
