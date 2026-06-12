<?php

namespace App\Api\AdminV1\Http\Requests\User;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
            'password' => ['required', 'max:191'],
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
            'password' => ['nullable', 'max:191'],
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
            'bank_name.required_if' => __('please_enter_bank_name'),
            'bank_account_number.required_if' => __('please_enter_bank_account_number'),
            'bank_account.required_if' => __('please_enter_bank_account'),
            'email.unique' => __('customer_email_unique'),
            'phone.unique' => __('phone_unique'),
            'password.max' => __('password_max'),
            'birthday.date_format' => __('customer_birthday_date'),
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('PUT')) {
                if (!isset($this->email) && !isset($this->phone)) {
                    $validator->errors()->add('email', __('user.must_provide_email_or_phone'));
                }
            }
            if (
                !isset($this->email) && isset($this->is_email_verified) && $this->is_email_verified == 1
            ) {
                    $validator->errors()->add('email', __('user.enter_new_email_to_verify'));
            }
            if (
                !isset($this->phone) && isset($this->is_phone_verified) && $this->is_phone_verified == 1
            ) {
                    $validator->errors()->add('phone', __('user.enter_new_phone_to_verify'));
            }
            if (
                !empty($this->province_id) && empty($this->ward_id)
            ) {
                    $validator->errors()->add('province_id', __('user.choose_full_address'));
            }
        });
    }
}
