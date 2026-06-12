<?php

namespace App\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;

class VerifyResetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodGet()
    {
        return [
            'email' =>['required', 'exists:App\Models\User,email'],
            'token_get_password' =>['required', 'digits:6'],
        ];
    }
}
