<?php

namespace App\Admin\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    protected function methodPut()
    {
        $guard = auth('admin')->check() ? 'admin' : 'web';

        return [
            'old_password' => ['required', 'string', 'max:255', "current_password:$guard"],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => 'Mật khẩu cũ là bắt buộc.',
            'old_password.current_password' => 'Mật khẩu cũ không đúng.',
            'password.required' => 'Mật khẩu mới là bắt buộc.',
            'password.string' => 'Mật khẩu mới phải là một chuỗi ký tự.',
            'password.max' => 'Mật khẩu mới không được vượt quá 255 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
