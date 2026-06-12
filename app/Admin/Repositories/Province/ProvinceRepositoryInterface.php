<?php

namespace App\Admin\Repositories\Province;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface ProvinceRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);
}