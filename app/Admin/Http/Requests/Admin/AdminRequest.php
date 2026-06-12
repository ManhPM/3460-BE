<?php

namespace App\Admin\Http\Requests\Admin;

use App\Admin\Http\Requests\BaseRequest;

class AdminRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'email' => ['required', 'email', 'unique:App\Models\Admin,email'],
            'fullname' => ['required', 'string'],
            'phone' => ['required', 'unique:App\Models\Admin,phone'],
            'password' => ['required', 'string', 'confirmed'],
            'branch_name' => ['nullable', 'string'],
            'branch_phone' => ['nullable', 'string'],
            'branch_address' => ['nullable', 'string'],
            'avatar' => ['nullable'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Admin,id'],
            'email' => ['required', 'email', 'unique:App\Models\Admin,email,' . $this->id],
            'fullname' => ['required', 'string'],
            'phone' => ['required', 'unique:App\Models\Admin,phone,' . $this->id],
            'password' => ['nullable', 'string', 'confirmed'],
            'branch_name' => ['nullable', 'string'],
            'branch_phone' => ['nullable', 'string'],
            'branch_address' => ['nullable', 'string'],
            'avatar' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'fullname.required' => 'Họ và tên là bắt buộc.',
            'fullname.string' => 'Họ và tên phải là chuỗi ký tự.',
            'phone.required' => 'Số điện thoại là bắt buộc.',

            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'id.required' => 'ID quản trị viên là bắt buộc.',
            'id.exists' => 'Quản trị viên không tồn tại.',
        ];
    }
}
