<?php

namespace App\Api\V1\Http\Requests\Affiliate;

use App\Api\V1\Http\Requests\BaseRequest;

class UpdateAffiliateRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}

