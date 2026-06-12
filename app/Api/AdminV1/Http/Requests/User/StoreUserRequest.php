<?php

namespace App\Api\AdminV1\Http\Requests\User;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreUserRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'status' => ['required', 'in:active,inactive,banned'],
        ];
    }
}
