<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'email' => 'required',
            'password' => 'required',
            'remember' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('please_enter_email'),
            'password.required' => __('please_enter_password'),
        ];
    }
}
