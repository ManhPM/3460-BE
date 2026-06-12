<?php

namespace App\Api\AdminV1\Http\Requests\CommissionWithdrawal;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateCommissionWithdrawalStatusRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
            'note' => ['nullable', 'string'],
        ];
    }
}

