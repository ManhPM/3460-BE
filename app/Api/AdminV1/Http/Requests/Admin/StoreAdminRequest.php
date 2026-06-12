<?php

namespace App\Api\AdminV1\Http\Requests\Admin;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreAdminRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}

