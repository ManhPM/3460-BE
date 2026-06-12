<?php

namespace App\Api\AdminV1\Http\Requests\Role;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateRoleRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:roles,name,' . $this->route('role')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }
}

