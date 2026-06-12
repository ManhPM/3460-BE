<?php

namespace App\Api\AdminV1\Services\Admin;

use App\Api\AdminV1\Repositories\Admin\AdminRepositoryInterface;
use Spatie\Permission\Models\Role;

class AdminService
{
    protected $repository;

    public function __construct(AdminRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data, $roleId = null)
    {
        $admin = $this->repository->create($data);

        if ($roleId) {
            $role = Role::findById($roleId, 'admin');
            if ($role) {
                $admin->assignRole($role);
            }
        }

        return $admin;
    }

    public function update(int $id, array $data, $roleId = null)
    {
        $admin = $this->repository->update($id, $data);

        if ($roleId !== null) {
            $roles = [];
            if ($roleId) {
                $role = Role::findById($roleId, 'admin');
                if ($role) {
                    $roles[] = $role;
                }
            }
            $admin->syncRoles($roles);
        }

        return $admin;
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
