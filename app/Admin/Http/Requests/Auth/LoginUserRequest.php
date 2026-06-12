<?php

namespace App\Admin\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;

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
            'username.required' => 'Vui lòng nhập email hoặc số điện thoại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
        ];
    }
}
