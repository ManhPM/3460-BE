<?php

namespace App\Api\AdminV1\Http\Requests\Bank;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateBankRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'bank_account' => ['sometimes', 'required', 'string', 'max:255'],
            'bank_account_number' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
