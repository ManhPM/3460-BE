<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;

class VerifyEmailRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'email' => ['required', 'exists:App\Models\User,email'],
            'verify_code' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'email.required' => __('please_enter_email'),
            'email.exists' => __('email_not_exists'),
            'verify_code.required' => __('please_enter_activation_token'),
        ];
    }
}
