<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class ResetPasswordRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'email' => ['required', 'exists:App\Models\User,email']
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $isExist = User::where('email', $this->email)->where('is_email_verified', 1)->first();

            if (!$isExist) {
                $validator->errors()->add('email', __('account_not_exists'));
            }
        });
    }
}
