<?php

namespace App\Admin\Repositories\WalletTransaction;

use App\Admin\Repositories\EloquentRepository;
use App\Models\WalletTransaction;

class WalletTransactionRepository extends EloquentRepository implements WalletTransactionRepositoryInterface
{
    protected $select = [];

    public function getModel()
    {
        return WalletTransaction::class;
    }
}


