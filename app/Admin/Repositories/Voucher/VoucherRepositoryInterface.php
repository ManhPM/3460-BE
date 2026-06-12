<?php

namespace App\Admin\Repositories\Voucher;

use App\Admin\Repositories\EloquentRepositoryInterface;


interface VoucherRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);
    public function getValid();
    public function getValidForUser($voucherType = null);
}
