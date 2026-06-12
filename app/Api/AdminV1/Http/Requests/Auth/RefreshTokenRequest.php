<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class RefreshTokenRequest extends BaseRequest
{
    /**
     * Get the validation rules for POST method
     */
    protected function methodPost(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'refresh_token.required' => __('please_enter_refresh_token'),
            'refresh_token.string' => __('refresh_token.string'),
        ];
    }
}

