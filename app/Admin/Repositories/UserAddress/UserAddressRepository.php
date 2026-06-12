<?php

namespace App\Admin\Repositories\UserAddress;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\UserAddress\UserAddressRepositoryInterface;
use App\Models\UserAddress;

class UserAddressRepository extends EloquentRepository implements UserAddressRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return UserAddress::class;
    }
}
