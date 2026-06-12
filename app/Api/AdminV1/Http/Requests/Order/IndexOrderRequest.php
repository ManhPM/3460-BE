<?php

namespace App\Api\AdminV1\Http\Requests\Order;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexOrderRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:pending,confirmed,processing,shipped,completed,cancelled'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid,failed,refunded'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'sort_by' => ['nullable', 'string', 'in:order_number,created_at,updated_at,total'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

