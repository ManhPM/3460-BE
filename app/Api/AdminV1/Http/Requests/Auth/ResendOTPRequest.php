<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class ResendOTPRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'code' => ['required', 'exists:users,code'],
        ];
    }
}

