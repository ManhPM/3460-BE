<?php

namespace App\Api\V1\Http\Requests\UserAddress;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\UserAddress;

class UserAddressRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'address' => ['required'],
            'phone' => ['required', 'regex:/^([0-9]{10})$/'],
            'email' => ['nullable', 'email'],
            'name' => ['required'],
            'fullname' => ['required'],
            'is_default' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\UserAddress,id'],
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'address' => ['required'],
            'phone' => ['required', 'regex:/^([0-9]{10})$/'],
            'email' => ['nullable', 'email'],
            'name' => ['required'],
            'fullname' => ['required'],
            'is_default' => ['required'],
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
                $validator->errors()->add('address', __('user_address.address_exists'));
            }
        });
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('Vui lòng nhập số điện thoại'),
            'phone.regex' => __('Số điện thoại phải có đúng 10 chữ số'),
        ];
    }
}
