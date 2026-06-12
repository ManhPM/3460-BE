<?php

namespace App\Admin\Http\Requests\Bank;

use App\Admin\Http\Requests\BaseRequest;

class BankRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Bank,id'],
            'is_active' => ['required'],
            'bank_account' => ['required'],
            'bank_account_number' => ['required'],
        ];
    }

    protected function methodPost(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Bank,id'],
            'is_active' => ['required'],
            'bank_account' => ['required'],
            'bank_account_number' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Trường ID là bắt buộc.',
            'id.exists' => 'ID ngân hàng không tồn tại.',
            'is_active.required' => 'Trạng thái hoạt động là bắt buộc.',
            'bank_account.required' => 'Tên tài khoản ngân hàng là bắt buộc.',
            'bank_account_number.required' => 'Số tài khoản ngân hàng là bắt buộc.',
        ];
    }
}
