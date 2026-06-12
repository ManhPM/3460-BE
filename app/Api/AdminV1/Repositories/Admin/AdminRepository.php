<?php

namespace App\Api\AdminV1\Repositories\Admin;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminRepository extends EloquentRepository implements AdminRepositoryInterface
{
    public function getModel(): string
    {
        return Admin::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('roles');

        // Column-specific filters - name
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        // Column-specific filters - fullname
        if (request()->has('fullname') && !empty(request('fullname'))) {
            $query->where('fullname', 'like', '%' . request('fullname') . '%');
        }

        // Column-specific filters - email
        if (request()->has('email') && !empty(request('email'))) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }

        // Column-specific filters - phone
        if (request()->has('phone') && !empty(request('phone'))) {
            $query->where('phone', 'like', '%' . request('phone') . '%');
        }

        // Column-specific filters - status (SELECT - exact match)
        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        // Filter by role (relationship search)
        if (request()->has('role_id') && !empty(request('role_id'))) {
            $query->whereHas('roles', function ($q) {
                $q->where('roles.id', request('role_id'));
            });
        }

        // Column-specific filters - created_at (DATE)
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', '%' . request('created_at') . '%');
        }

        // Column-specific filters - updated_at (DATE)
        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', '%' . request('updated_at') . '%');
        }

        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = request('per_page', 10);
        return $query->paginate($perPage);
    }

    public function findOrFailWithRelations($id)
    {
        return $this->model->with('roles')->findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return parent::create($data);
    }

    public function update($id, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return parent::update($id, $data);
    }
}
