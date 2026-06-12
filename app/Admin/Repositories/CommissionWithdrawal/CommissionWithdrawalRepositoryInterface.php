<?php

namespace App\Admin\Repositories\CommissionWithdrawal;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface CommissionWithdrawalRepositoryInterface extends EloquentRepositoryInterface
{
    public function getTotalWithdraw($userId);
    public function withdrawHistory($userId);
}
