<?php

namespace App\Api\AdminV1\Repositories\Admin;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface AdminRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function findOrFailWithRelations($id);
}
