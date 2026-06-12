<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class PasswordResetUpdateRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'email' => ['required', 'exists:App\Models\User,email'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('please_enter_email'),
            'email.email' => __('email_invalid'),
            'email.exists' => __('email_not_exists'),
            'password.required' => __('please_enter_password'),
            'password.min' => __('password_min'),
            'password.confirmed' => __('password_confirmed'),
        ];
    }
}
