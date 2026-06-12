<?php

namespace App\Api\AdminV1\Repositories\Role;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Role;

class RoleRepository extends EloquentRepository implements RoleRepositoryInterface
{
    public function getModel(): string
    {
        return Role::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('permissions');

        // Column-specific filters - name
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        // Column-specific filters - title
        if (request()->has('title') && !empty(request('title'))) {
            $query->where('title', 'like', '%' . request('title') . '%');
        }

        // Column-specific filters - guard_name (SELECT - exact match)
        if (request()->has('guard_name') && request('guard_name') !== '' && request('guard_name') !== null) {
            $query->where('guard_name', request('guard_name'));
        }

        // Column-specific filters - created_at (DATE)
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', '%' . request('created_at') . '%');
        }

        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = request('per_page', 10);
        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = parent::create($data);

        if (!empty($permissions)) {
            $role->permissions()->sync($permissions);
        }

        return $role->load('permissions');
    }

    public function update($id, array $data)
    {
        $permissions = $data['permissions'] ?? null;
        unset($data['permissions']);

        $role = parent::update($id, $data);

        if ($permissions !== null) {
            $role->permissions()->sync($permissions);
        }

        return $role->load('permissions');
    }
}

