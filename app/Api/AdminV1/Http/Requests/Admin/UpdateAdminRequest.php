<?php

namespace App\Api\AdminV1\Http\Requests\Admin;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateAdminRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:admins,email,' . $this->route('admin')],
            'password' => ['sometimes', 'nullable', 'string', 'min:6', 'confirmed'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}

