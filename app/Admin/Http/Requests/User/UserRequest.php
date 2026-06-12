<?php

namespace App\Admin\Http\Requests\User;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'fullname' => ['required', 'string'],
            'province_id' => ['nullable', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'phone' => [
                'nullable',
            ],
            'email' => ['nullable', 'email', 'unique:App\Models\User,email'],
            'password' => ['required', 'string', 'confirmed'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'birthday' => ['nullable'],
            'bank_name' => ['nullable',],
            'bank_account_number' => ['nullable',],
            'bank_account' => ['nullable',],
            'avatar' => ['nullable'],
            'is_email_verified' => ['required'],
            'is_phone_verified' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\User,id'],
            'province_id' => ['nullable', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'fullname' => ['required', 'string'],
            'bank_name' => ['nullable',],
            'bank_account_number' => ['nullable',],
            'bank_account' => ['nullable',],
            'email' => ['nullable', 'email', 'unique:App\Models\User,email,' . $this->id],
            'phone' => [
                'nullable',
            ],
            'password' => ['nullable', 'string', 'confirmed'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'birthday' => ['nullable'],
            'avatar' => ['nullable'],

            'is_email_verified' => ['required'],
            'is_phone_verified' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'bank_name.required_if' => 'Tên ngân hàng không được để trống',
            'bank_account_number.required_if' => 'Số tài khoản ngân hàng không được để trống',
            'bank_account.required_if' => 'Tài khoản ngân hàng không được để trống',
            'email.unique' => 'Email đã tồn tại',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'birthday.date_format' => 'Ngày sinh không hợp lệ',
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('PUT')) {
                if (!isset($this->email) && !isset($this->phone)) {
                    $validator->errors()->add('email', 'Bạn phải nhập 1 trong 2 trường: Email, Số điện thoại.');
                }
            }
            if (
                !isset($this->email) && isset($this->is_email_verified) && $this->is_email_verified == 1
            ) {
                $validator->errors()->add('email', __('Vui lòng nhập email mới có thể xác minh email.'));
            }
            if (
                !isset($this->phone) && isset($this->is_phone_verified) && $this->is_phone_verified == 1
            ) {
                $validator->errors()->add('phone', __('Vui lòng nhập số điện thoại mới có thể xác minh số điện thoại.'));
            }
            if (
                !empty($this->province_id) && empty($this->ward_id)
            ) {
                $validator->errors()->add('province_id', __('Vui lòng chọn đầy đủ thông tin địa chỉ.'));
            }
        });
    }
}
