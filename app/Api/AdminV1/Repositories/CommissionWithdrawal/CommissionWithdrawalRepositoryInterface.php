<?php

namespace App\Api\AdminV1\Repositories\CommissionWithdrawal;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface CommissionWithdrawalRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

