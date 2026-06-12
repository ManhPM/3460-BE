<?php

namespace App\Admin\Http\Requests\WalletTransaction;

use App\Admin\Http\Requests\BaseRequest;
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
            'user_id' => 'required|exists:App\Models\User,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:deposit,withdraw,payment,refund',
            'status' => 'required|in:pending,approved,rejected',
            'note' => 'nullable|string',
            'order_id' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => __('Vui lòng chọn người dùng.'),
            'user_id.exists' => __('Người dùng không tồn tại.'),
            'amount.required' => __('Vui lòng nhập số tiền.'),
            'amount.numeric' => __('Số tiền không hợp lệ.'),
            'amount.min' => __('Số tiền phải >= 0.'),
            'type.required' => __('Vui lòng chọn loại giao dịch.'),
            'type.in' => __('Loại giao dịch không hợp lệ.'),
            'status.required' => __('Vui lòng chọn trạng thái.'),
            'status.in' => __('Trạng thái không hợp lệ.'),
        ];
    }
}


