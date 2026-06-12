<?php

namespace App\Api\AdminV1\Http\Requests\User;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexUserRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'sort_by' => ['nullable', 'string', 'in:name,email,created_at,updated_at'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

