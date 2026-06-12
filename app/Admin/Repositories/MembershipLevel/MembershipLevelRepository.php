<?php

namespace App\Admin\Repositories\MembershipLevel;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Models\MembershipLevel;

class MembershipLevelRepository extends EloquentRepository implements MembershipLevelRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return MembershipLevel::class;
    }
}
