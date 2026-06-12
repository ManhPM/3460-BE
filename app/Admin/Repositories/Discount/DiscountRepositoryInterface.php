<?php

namespace App\Admin\Repositories\Discount;

use App\Admin\Repositories\EloquentRepositoryInterface;


interface DiscountRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);
    public function getValid();
}
