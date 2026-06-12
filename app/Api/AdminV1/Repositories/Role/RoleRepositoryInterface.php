<?php

namespace App\Api\AdminV1\Repositories\Role;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface RoleRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

