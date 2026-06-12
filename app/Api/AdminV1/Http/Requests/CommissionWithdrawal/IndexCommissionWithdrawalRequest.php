<?php

namespace App\Api\AdminV1\Http\Requests\CommissionWithdrawal;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexCommissionWithdrawalRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

