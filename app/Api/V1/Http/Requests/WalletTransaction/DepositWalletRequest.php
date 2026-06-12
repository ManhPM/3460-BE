<?php

namespace App\Api\V1\Http\Requests\WalletTransaction;

use App\Api\V1\Http\Requests\BaseRequest;

class DepositWalletRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'amount' => ['required', 'numeric', 'min:50000', 'max:10000000'],
            'proof_image' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => __('please_enter_amount'),
            'amount.numeric' => __('amount_numeric'),
            'amount.min' => __('wallet_transaction.amount_min_50000'),
            'amount.max' => __('wallet_transaction.amount_max_10000000'),
        ];
    }
}
