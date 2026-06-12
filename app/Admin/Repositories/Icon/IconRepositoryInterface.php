<?php

namespace App\Admin\Repositories\Icon;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface IconRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);
}
