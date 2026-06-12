<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class SendMailResetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPost()
    {
        return [
            'email' =>['required', 'exists:App\Models\User,email'],
        ];
    }
}

