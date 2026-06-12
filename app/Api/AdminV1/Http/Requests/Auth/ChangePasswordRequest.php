<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    protected function methodPut()
    {
        return $this->methodPost();
    }

    protected function methodPost()
    {
        $guard = auth('admin')->check() || auth('admin-api')->check() ? 'admin' : 'web';

        return [
            'current_password' => ['required_without:old_password', 'string', 'max:255'],
            'old_password' => ['required_without:current_password', 'string', 'max:255', "current_password:$guard"],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'password_confirmation' => ['required_with:password', 'string'],
            'new_password' => ['required_without:password', 'string', 'min:8', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => __('please_enter_old_password'),
            'old_password.current_password' => __('change_password_old_incorrect'),
            'password.required' => __('please_enter_new_password'),
            'password.string' => __('change_password_string'),
            'password.max' => __('change_password_max'),
            'password.confirmed' => __('change_password_confirmed'),
        ];
    }
}
