<?php

namespace App\Api\AdminV1\Repositories\Permission;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface PermissionRepositoryInterface extends EloquentRepositoryInterface
{
    public function all();
}

