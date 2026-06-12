<?php

namespace App\Api\AdminV1\Http\Requests\Review;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexReviewRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'status' => ['nullable', 'string', 'in:pending,approved,rejected'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_by' => ['nullable', 'string', 'in:created_at,updated_at,rating'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

