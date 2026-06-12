<?php

namespace App\Admin\Http\Requests\CommissionWithdrawal;

use App\Admin\Http\Requests\BaseRequest;
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
                    $validator->errors()->add('amount', __('Bạn đã yêu cầu rút tiền một lần trước đó. Hãy chờ admin xác nhận'));
                }

                if ($this->amount > auth()->user()->wallet_balance) {
                    $validator->errors()->add('amount', __('Số dư không đủ'));
                }
                if ($this->amount < 50000) {
                    $validator->errors()->add('amount', __('Số tiền tối thiểu để rút là 50.000'));
                }
            }
        });
    }
}
