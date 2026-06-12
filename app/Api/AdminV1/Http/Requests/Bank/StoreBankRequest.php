<?php

namespace App\Api\AdminV1\Http\Requests\Bank;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreBankRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'bank_account' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'bank_id' => ['required', 'exists:banks,id'],
        ];
    }
}
