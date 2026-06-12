<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class LoginUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'username' => 'required',
            'password' => 'required',
            'remember' => 'nullable'
        ];
    }
    public function messages()
    {
        return [
            'username.required' => __('please_enter_email_or_phone'),
            'password.required' => __('please_enter_password'),
        ];
    }
}

