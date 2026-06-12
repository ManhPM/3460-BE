<?php

namespace App\Admin\Repositories\CommissionWithdrawal;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Enums\WithdrawStatus;
use App\Models\CommissionWithdrawal;

class CommissionWithdrawalRepository extends EloquentRepository implements CommissionWithdrawalRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return CommissionWithdrawal::class;
    }

    public function getTotalWithdraw($userId)
    {
        $this->getQueryBuilder();

        $this->instance = $this->instance
            ->where('status', WithdrawStatus::Confirmed)->where('user_id', $userId);

        return $this->instance->sum('amount');
    }

    public function withdrawHistory($userId)
    {
        $this->getQueryBuilder();

        $this->instance = $this->instance
            ->where('user_id', $userId)->paginate(8);

        return $this->instance;
    }
}
