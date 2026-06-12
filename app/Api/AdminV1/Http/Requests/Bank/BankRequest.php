<?php

namespace App\Api\AdminV1\Http\Requests\Bank;

use App\Api\AdminV1\Http\Requests\BaseRequest;

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
            'id.required' => __('please_enter_id'),
            'id.exists' => __('bank.not_exists'),
            'is_active.required' => __('please_choose_active_status'),
            'bank_account.required' => __('please_enter_bank_account'),
            'bank_account_number.required' => __('please_enter_bank_account_number'),
        ];
    }
}
