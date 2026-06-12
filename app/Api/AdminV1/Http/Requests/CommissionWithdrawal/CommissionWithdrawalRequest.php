<?php

namespace App\Api\AdminV1\Http\Requests\CommissionWithdrawal;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepository;
use App\Admin\Repositories\Order\OrderDetailRepository;
use App\Enums\WithdrawStatus;
use App\Models\CommissionWithdrawal;
use Illuminate\Validation\Rules\Enum;

class CommissionWithdrawalRequest extends BaseRequest
{
    public function methodPost()
    {
        return [
            'amount' => ['required', 'numeric', 'min:50000'],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\CommissionWithdrawal,id'],
            'status' => ['required', new Enum(WithdrawStatus::class)],
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
                    $validator->errors()->add('amount', __('insufficient_wallet_balance'));
                }
                if ($this->amount < 50000) {
                    $validator->errors()->add('amount', __('commission_withdrawal.min_amount_50000'));
                }
            }
        });
    }
}

