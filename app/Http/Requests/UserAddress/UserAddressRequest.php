<?php

namespace App\Http\Requests\UserAddress;

use App\Admin\Http\Requests\BaseRequest;
use App\Models\UserAddress;

class UserAddressRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['required', 'exists:App\Models\Ward,id'],
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
            $userId = auth()->id();
            $data = $this->only(['province_id', 'ward_id', 'address', 'phone', 'name']);

            $query = UserAddress::where($data)->where('user_id', $userId);

            if ($this->isMethod('put')) {
                $query->where('id', '!=', $this->id);
            }

            if ($query->exists()) {
                $validator->errors()->add('address', 'Địa chỉ này đã tồn tại.');
            }
        });
    }

    public function messages()
    {
        return [
            'province_id.required' => 'Tỉnh/Thành phố là bắt buộc.',
            'province_id.exists' => 'Tỉnh/Thành phố không tồn tại.',
            'ward_id.required' => 'Thành phố/Khu vực là bắt buộc.',
            'ward_id.exists' => 'Thành phố/Khu vực không tồn tại.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'phone.required' => 'Số điện thoại là bắt buộc.',

            'email.email' => 'Email không hợp lệ.',
            'name.required' => 'Tên địa điểm nhận là bắt buộc.',
            'fullname.required' => 'Tên người nhận là bắt buộc.',
            'id.required' => 'ID địa chỉ là bắt buộc.',
            'id.exists' => 'Địa chỉ không tồn tại.',
        ];
    }
}
