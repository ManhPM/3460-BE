<?php

namespace App\Api\AdminV1\Http\Requests\Bank;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexBankRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
        ];
    }
}
