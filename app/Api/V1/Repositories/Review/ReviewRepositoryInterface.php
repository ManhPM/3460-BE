<?php

namespace App\Api\V1\Repositories\Review;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ReviewRepositoryInterface extends EloquentRepositoryInterface
{
    public function getReviewsByOrderId($orderId, $userId);

    public function getReviewsByProductId($productId, $limit = 10);
}
