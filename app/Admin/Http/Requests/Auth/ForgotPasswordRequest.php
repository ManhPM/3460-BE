<?php

namespace App\Admin\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;

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
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'token_get_password.required' => 'Mã đặt lại mật khẩu là bắt buộc.',
            'password.required' => 'Mật khẩu mới là bắt buộc.',
            'password.string' => 'Mật khẩu mới phải là một chuỗi ký tự.',
            'confirm.required' => 'Xác nhận mật khẩu là bắt buộc.',
            'confirm.string' => 'Xác nhận mật khẩu phải là một chuỗi ký tự.',
            'confirm.same' => 'Xác nhận mật khẩu không khớp với mật khẩu mới.',
        ];
    }
}
