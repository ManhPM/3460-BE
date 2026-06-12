<?php

namespace App\Api\AdminV1\Repositories\Bank;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface BankRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();

    public function getUniqueBanks();
}
