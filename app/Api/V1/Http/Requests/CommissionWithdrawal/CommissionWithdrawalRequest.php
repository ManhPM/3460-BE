<?php

namespace App\Api\V1\Http\Requests\CommissionWithdrawal;

use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepository;
use App\Admin\Repositories\Order\OrderDetailRepository;
use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\WithdrawStatus;
use App\Models\CommissionWithdrawal;

class CommissionWithdrawalRequest extends BaseRequest
{
    public function methodPost()
    {
        return [
            'amount' => ['required', 'numeric', 'min:50000'],
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => __('please_enter_amount'),
            'amount.numeric' => __('amount_numeric'),
            'amount.min' => __('commission_withdrawal.min_amount_50000'),
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra nếu method là POST
            if (request()->isMethod('POST')) {
                $userId = auth()->id();

                $isExist = CommissionWithdrawal::where('user_id', $userId)->where('status', WithdrawStatus::Pending)->first();
                if ($isExist) {
                    $validator->errors()->add('amount', __('commission_withdrawal.pending_request_exists'));
                }

                if ($this->amount > auth()->user()->wallet_balance) {
                    $validator->errors()->add('amount', __('wallet_transaction.insufficient_balance'));
                }
            }
        });
    }
}
