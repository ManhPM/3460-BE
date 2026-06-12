<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class ForgotPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'email' => 'required|email',
        ];
    }

    protected function methodPost()
    {
        return [
            'email' => 'required|email',
        ];
    }

    protected function methodPut()
    {
        return [
            'token_get_password' => 'required',
            'password' => 'required|string',
            'confirm' => 'required|string|same:password',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('please_enter_email'),
            'email.email' => __('email_invalid'),
            'token_get_password.required' => __('please_enter_reset_password_token'),
            'password.required' => __('please_enter_new_password'),
            'password.string' => __('forgot_password.password_string'),
            'confirm.required' => __('please_enter_password_confirm'),
            'confirm.string' => __('forgot_password.confirm_string'),
            'confirm.same' => __('forgot_password.confirm_same'),
        ];
    }
}

