<?php

namespace App\Admin\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class ProfileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPut()
    {
        if (auth('admin')->user()) {
            $this->validate = [
                'fullname' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'unique:App\Models\Admin,phone,' . auth('admin')->user()->id],
                'email' => ['nullable'],
                'avatar' => ['nullable'],
                'branch_name' => ['nullable'],
                'branch_phone' => ['nullable'],
                'branch_address' => ['nullable'],
            ];
            return $this->validate;
        } else {
            $this->validate = [
                'fullname' => ['required', 'string', 'max:255'],
                'email' => ['nullable'],
                'phone' => ['nullable'],
                'address' => ['nullable'],
                'birthday' => ['nullable'],
                'gender' => ['nullable'],
                'avatar' => ['nullable'],
            ];
            return $this->validate;
        }
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (!auth('admin')->user()) {
                $email = $this->email;
                $phone = $this->phone;
                $user = auth()->user();

                if (
                    $email && User::where('email', $email)
                    ->where('is_email_verified', 1)
                    ->where('id', '!=', $user->id)
                    ->exists()
                ) {
                    $validator->errors()->add('email', __('Email đã được đăng ký.'));
                }

                if (
                    $phone && User::where('phone', $phone)
                    ->where('is_phone_verified', 1)
                    ->where('id', '!=', $user->id)
                    ->exists()
                ) {
                    $validator->errors()->add('phone', __('Số điện thoại đã được đăng ký.'));
                }
            }
        });
    }
}
