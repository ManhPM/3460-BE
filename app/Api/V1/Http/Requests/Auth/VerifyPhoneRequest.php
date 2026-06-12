<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class VerifyPhoneRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'phone' => ['required'],
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $user = User::firstWhere('phone', $this->phone);
            if ($user) {
                $validator->errors()->add('phone', __('auth.phone_already_verified_another_account'));
            }
        });
    }
}
