<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResendOTPRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'email' => ['required', 'email', 'max:255', 'exists:users,email'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('please_enter_email'),
            'email.email' => __('email_invalid'),
            'email.max' => __('email_max'),
            'email.exists' => __('email_not_registered'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => __('data_invalid'),
            'errors' => $validator->errors(),
        ], 422));
    }
}
