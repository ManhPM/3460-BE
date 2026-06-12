<?php

namespace App\Api\AdminV1\Http\Requests\Setting;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateSettingRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string', 'max:255'],
            'settings.*.value' => ['nullable', 'string'],
        ];
    }
}

