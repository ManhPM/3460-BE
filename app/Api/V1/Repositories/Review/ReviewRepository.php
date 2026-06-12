<?php

namespace App\Api\V1\Repositories\Review;

use App\Admin\Repositories\EloquentRepository;
use App\Api\V1\Repositories\Review\ReviewRepositoryInterface;
use App\Models\OrderDetail;
use App\Models\Review;

class ReviewRepository extends EloquentRepository implements ReviewRepositoryInterface
{
    public function getModel()
    {
        return Review::class;
    }

    public function getReviewsByOrderId($orderId, $userId)
    {

        $productIds = OrderDetail::where('order_id', $orderId)
            ->pluck('product_id'); // Get an array of product IDs

        // Step 2: Query the reviews based on product IDs and user ID
        $reviews = $this->model->whereIn('product_id', $productIds)
            ->where('user_id', $userId)
            ->with('user') // Optional: Include user information in the result
            ->get();

        return $reviews;
    }

    public function getReviewsByProductId($productId, $limit = 10)
    {
        return $this->model
            ->where('product_id', $productId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->simplePaginate($limit);
    }
}
