<?php

namespace App\Api\AdminV1\Repositories\WalletTransaction;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface WalletTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

