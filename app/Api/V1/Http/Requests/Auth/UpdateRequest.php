<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\User\Gender;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'fullname' => ['nullable', 'string'],
            'birthday' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'address' => ['nullable'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'avatar' => ['nullable'],
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $email = $this->email;
            $phone = $this->phone;
            $userId = auth()->id();

            if (
                $email && User::where('email', $email)
                ->where('is_email_verified', 1)
                ->where('id', '!=', $userId)
                ->exists()
            ) {
                $validator->errors()->add('email', __('auth.email_already_registered'));
            }

            if (
                $phone && User::where('phone', $phone)
                ->where('is_phone_verified', 1)
                ->where('id', '!=', $userId)
                ->exists()
            ) {
                $validator->errors()->add('phone', __('auth.phone_already_registered'));
            }
        });
    }

    public function messages()
    {
        return [
            'fullname.string' => __('name_string'),
            'birthday.string' => __('birthday_string'),
            'email.email' => __('email_invalid'),

            'gender.enum' => __('gender_invalid'),
            'province_id.exists' => __('province_not_exists'),
            'ward_id.exists' => __('ward_not_exists'),
            'bank_name.required_if' => __('please_enter_bank_name'),
            'bank_account_number.required_if' => __('please_enter_bank_account_number'),
            'bank_account.required_if' => __('please_enter_bank_account'),
        ];
    }
}
