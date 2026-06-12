<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
        return $this->methodPost();
    }

    protected function methodPost()
    {
        if (auth('admin')->user() || auth('admin-api')->check()) {
            $adminId = auth('admin')->user()?->id ?? auth('admin-api')->user()?->id;
            $this->validate = [
                'name' => ['required', 'string', 'max:255'],
                'fullname' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:App\Models\Admin,email,' . $adminId],
                'avatar' => ['nullable'], // Accept both file and base64 string
                'phone' => ['nullable', 'unique:App\Models\Admin,phone,' . $adminId],
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

