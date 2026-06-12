<?php

namespace App\Api\AdminV1\Repositories\Review;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Review;

class ReviewRepository extends EloquentRepository implements ReviewRepositoryInterface
{
    public function getModel(): string
    {
        return Review::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with(['user', 'product']);

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        // Search user by name or email
        if (request()->has('user_id') && !empty(request('user_id'))) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('fullname', 'like', "%" . request('user_id') . "%")
                    ->orWhere('email', 'like', "%" . request('user_id') . "%");
            });
        }

        // Search product by name
        if (request()->has('product_id') && !empty(request('product_id'))) {
            $query->whereHas('product', function ($productQuery) {
                $productQuery->where('name', 'like', "%" . request('product_id') . "%");
            });
        }

        // Select/Dropdown - Exact match
        if (request()->has('rating') && request('rating') !== '' && request('rating') !== null) {
            $query->where('rating', request('rating'));
        }

        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        if (request()->has('content') && !empty(request('content'))) {
            $query->where('content', 'like', "%" . request('content') . "%");
        }

        // Date/Datetime - Dùng like
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }



    public function reply(int $id, string $reply)
    {
        $review = $this->model->findOrFail($id);
        $review->update(['admin_reply' => $reply]);
        return $review;
    }

    public function approve(int $id)
    {
        $review = $this->model->findOrFail($id);
        $review->update(['status' => 'approved']);
        return $review;
    }

    public function reject(int $id)
    {
        $review = $this->model->findOrFail($id);
        $review->update(['status' => 'rejected']);
        return $review;
    }
}
