<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'fullname' => ['required', 'string'],
            'phone' => [
                'required',
            ],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $email = $this->email;
            $phone = $this->phone;

            if ($email && User::where('email', $email)->where('is_email_verified', 1)->exists()) {
                $validator->errors()->add('email', __('Email đã được đăng ký.'));
            }

            if ($phone && User::where('phone', $phone)->where('is_phone_verified', 1)->exists()) {
                $validator->errors()->add('phone', __('Số điện thoại đã được đăng ký.'));
            }
        });
    }
}

