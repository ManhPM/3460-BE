<?php

namespace App\Admin\Repositories\Bank;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface BankRepositoryInterface extends EloquentRepositoryInterface
{
    public function getActiveBank($column = 'id', $sort = 'DESC');
}
