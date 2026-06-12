<?php

namespace App\Api\AdminV1\Http\Requests\User;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'unique:users,email,' . $userId],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['sometimes', 'required', 'in:active,inactive,banned'],
        ];
    }
}
