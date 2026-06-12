<?php

namespace App\Admin\Http\Requests\UserAddress;

use App\Admin\Http\Requests\BaseRequest;
use App\Models\UserAddress;

class UserAddressRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'user_id' => ['required', 'exists:App\Models\User,id'],
            'address' => ['required'],
            'phone' => ['required'],
            'email' => ['nullable', 'email'],
            'name' => ['required'],
            'fullname' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\UserAddress,id'],
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'address' => ['required'],
            'phone' => ['required'],
            'email' => ['nullable', 'email'],
            'name' => ['required'],
            'fullname' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->only(['province_id', 'ward_id', 'address', 'phone', 'name']);

            $query = UserAddress::where($data);

            if ($this->isMethod('put')) {
                $query->where('id', '!=', $this->id);
            } else {
                $query->where('user_id', $this->user_id);
            }

            if ($query->exists()) {
                $validator->errors()->add('address', 'Địa chỉ này đã tồn tại.');
            }
        });
    }

    public function messages()
    {
        return [
            'province_id.required' => 'Tỉnh/Thành phố không được để trống.',
            'ward_id.required' => 'Thành phố/Khu vực không được để trống.',
            'address.required' => 'Địa chỉ không được để trống.',
            'phone.required' => 'Số điện thoại không được để trống.',

            'email.email' => 'Email không hợp lệ.',
            'name.required' => 'Tên địa điểm nhận không được để trống.',
            'fullname.required' => 'Tên người nhận không được để trống.',
        ];
    }
}
