<?php

namespace App\Api\AdminV1\Http\Requests\Notification;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreNotificationRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_message' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string'],
        ];
    }
}

