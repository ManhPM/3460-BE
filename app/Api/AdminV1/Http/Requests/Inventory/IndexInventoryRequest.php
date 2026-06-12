<?php

namespace App\Api\AdminV1\Http\Requests\Inventory;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexInventoryRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'low_stock' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', 'in:product_id,quantity,created_at,updated_at'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

