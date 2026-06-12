<?php

namespace App\Api\AdminV1\Http\Requests\Admin;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexAdminRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort_by' => ['nullable', 'string', 'in:created_at,name,email'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

