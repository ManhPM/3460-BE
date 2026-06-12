<?php

namespace App\Api\AdminV1\Http\Requests\WalletTransaction;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Models\User;

class WalletTransactionRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'user_id' => 'required|exists:App\Models\User,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:deposit,withdraw,payment,refund',
            'status' => 'required|in:pending,approved,rejected',
            'note' => 'nullable|string',
            'order_id' => 'nullable|integer',
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => 'required|exists:App\Models\WalletTransaction,id',
            'note' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => __('please_choose_user'),
            'user_id.exists' => __('user_not_found'),
            'amount.required' => __('please_enter_amount'),
            'amount.numeric' => __('transaction_amount_integer'),
            'amount.min' => __('transaction_amount_min'),
            'type.required' => __('please_choose_transaction_type'),
            'type.in' => __('transaction_payment_method_invalid'),
            'status.required' => __('please_choose_status'),
            'status.in' => __('transaction_status_invalid'),
        ];
    }
}

