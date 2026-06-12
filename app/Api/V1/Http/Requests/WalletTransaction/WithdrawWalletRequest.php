<?php

namespace App\Api\V1\Http\Requests\WalletTransaction;

use App\Api\V1\Http\Requests\BaseRequest;

class WithdrawWalletRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'amount' => ['required', 'numeric', 'min:50000', 'max:10000000'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function withValidator($validator)
    {
        if ($this->isMethod('post')) {
            $validator->after(function ($validator) {
                $user = auth('user')->user();
                if ($user && $user->wallet_balance < $this->amount) {
                    $validator->errors()->add('amount', __('wallet_transaction.insufficient_balance'));
                }
            });
        }
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
