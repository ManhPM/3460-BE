<?php

namespace App\Api\AdminV1\Http\Requests\Discount;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexDiscountRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'sort_by' => ['nullable', 'string', 'in:name,created_at,updated_at'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

