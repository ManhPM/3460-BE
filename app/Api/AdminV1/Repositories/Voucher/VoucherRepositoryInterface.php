<?php

namespace App\Api\AdminV1\Repositories\Voucher;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface VoucherRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function toggleStatus(int $id);
}
