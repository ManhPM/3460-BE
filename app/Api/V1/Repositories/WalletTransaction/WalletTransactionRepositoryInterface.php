<?php

namespace App\Api\V1\Repositories\WalletTransaction;

interface WalletTransactionRepositoryInterface
{
    public function getByUserAndType($userId, $type = null, $page = 1, $limit = 10, $period = null, $startDate = null, $endDate = null);
}
