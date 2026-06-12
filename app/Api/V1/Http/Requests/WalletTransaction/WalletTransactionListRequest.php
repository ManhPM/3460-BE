<?php

namespace App\Api\V1\Http\Requests\WalletTransaction;

use App\Api\V1\Http\Requests\BaseRequest;

class WalletTransactionListRequest extends BaseRequest
{
    protected function methodGet()
    {
        return [
            'type' => ['nullable', 'string', 'in:deposit,withdraw,payment,refund,affiliate'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'period' => ['nullable', 'string', 'in:today,this_week,this_month,this_year,custom'],
            'start_date' => ['nullable', 'date', 'required_if:period,custom'],
            'end_date' => ['nullable', 'date', 'required_if:period,custom', 'after_or_equal:start_date'],
        ];
    }
}
