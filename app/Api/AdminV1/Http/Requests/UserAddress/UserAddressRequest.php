<?php

namespace App\Api\AdminV1\Http\Requests\UserAddress;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
                $validator->errors()->add('address', __('user_address.address_exists'));
            }
        });
    }

    public function messages()
    {
        return [
            'province_id.required' => __('please_choose_province'),
            'ward_id.required' => __('please_choose_ward'),
            'address.required' => __('please_enter_address'),
            'phone.required' => __('please_enter_phone'),

            'email.email' => __('email_invalid'),
            'name.required' => __('please_enter_address_name'),
            'fullname.required' => __('please_enter_fullname'),
        ];
    }
}
