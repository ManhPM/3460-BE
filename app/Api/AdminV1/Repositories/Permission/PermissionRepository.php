<?php

namespace App\Api\AdminV1\Repositories\Permission;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Permission;

class PermissionRepository extends EloquentRepository implements PermissionRepositoryInterface
{
    public function getModel(): string
    {
        return Permission::class;
    }

    public function all()
    {
        return $this->model->with('module')->orderBy('name', 'asc')->get();
    }
}
