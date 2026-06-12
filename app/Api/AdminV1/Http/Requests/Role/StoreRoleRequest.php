<?php

namespace App\Api\AdminV1\Http\Requests\Role;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreRoleRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }
}

