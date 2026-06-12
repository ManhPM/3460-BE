<?php

namespace App\Admin\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;

class OauthReqest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'code' => 'required|exists:users,code',
        ];
    }
    protected function methodPost()
    {
        return [
            'code' => 'required',
            'verify_code' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Vui lòng nhập mã xác thực.',
            'code.exists' => 'Mã xác thực không hợp lệ.',
            'verify_code.required' => 'Vui lòng nhập token kích hoạt tài khoản.',
            'verify_code.numeric' => 'Token kích hoạt tài khoản phải là số.',
        ];
    }
}
