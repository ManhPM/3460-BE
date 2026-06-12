<?php

namespace App\Admin\Repositories\Review;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Review\ReviewRepositoryInterface;
use App\Models\Review;

class ReviewRepository extends EloquentRepository implements ReviewRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Review::class;
    }
}
