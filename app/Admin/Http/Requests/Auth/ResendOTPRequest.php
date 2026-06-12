<?php

namespace App\Admin\Http\Requests\Auth;

use App\Admin\Http\Requests\BaseRequest;

class ResendOTPRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'code' => ['required', 'exists:users,code'],
        ];
    }
}
