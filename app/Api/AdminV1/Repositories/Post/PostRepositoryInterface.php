<?php

namespace App\Api\AdminV1\Repositories\Post;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface PostRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

