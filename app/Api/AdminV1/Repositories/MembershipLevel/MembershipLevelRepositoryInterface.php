<?php

namespace App\Api\AdminV1\Repositories\MembershipLevel;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface MembershipLevelRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

