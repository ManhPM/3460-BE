<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;

class UpdatePasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPost()
    {
        return [
            'old_password' => ['required', 'current_password'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => __('please_enter_old_password'),
            'old_password.current_password' => __('change_password_old_incorrect'),
            'password.required' => __('please_enter_new_password'),
            'password.string' => __('change_password_string'),
            'password.confirmed' => __('change_password_confirmed'),
        ];
    }
}
