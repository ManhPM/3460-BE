<?php

namespace App\Api\AdminV1\Repositories\PostCategory;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface PostCategoryRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

