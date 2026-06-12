<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'fullname' => ['required', 'string'],
            'phone' => [
                'nullable',
            ],
            'bank_name' => [
                'nullable',
            ],
            'bank_account_number' => [
                'nullable',
            ],
            'bank_account' => [
                'nullable',
            ],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Configure the validator to include an after validation hook.
     *
     * @return void
     */
    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $email = $this->email;

            if ($email && User::where('email', $email)->where('is_email_verified', 1)->exists()) {
                $validator->errors()->add('email', __('auth.email_already_registered'));
            }

            if ($this->phone && User::where('phone', $this->phone)->where('is_phone_verified', 1)->exists()) {
                $validator->errors()->add('phone', __('auth.phone_already_registered'));
            }
        });
    }

    public function messages()
    {
        return [
            'fullname.required' => __('please_enter_fullname'),
            'fullname.max' => __('fullname_max'),
            'phone.regex' => __('phone_invalid'),
            'phone.unique' => __('phone_unique'),
            'email.required' => __('please_enter_email'),
            'email.email' => __('email_invalid'),
            'email.unique' => __('email_unique'),
            'password.required' => __('please_enter_password'),
            'password.min' => __('password_min'),
            'password.confirmed' => __('password_confirmed'),
            'bank_name.required_if' => __('please_enter_bank_name'),
            'bank_account_number.required_if' => __('please_enter_bank_account_number'),
            'bank_account.required_if' => __('please_enter_bank_account'),
        ];
    }
}
