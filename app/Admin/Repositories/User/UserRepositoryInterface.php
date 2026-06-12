<?php

namespace App\Admin\Repositories\User;
use App\Admin\Repositories\EloquentRepositoryInterface;
use App\Models\Role;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($value = '', $meta = [], $select = [], $limit = 10, $role);
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
    public function getUserByRole($role);
}
