<?php

namespace App\Api\AdminV1\Http\Requests\Voucher;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreVoucherRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'code' => ['required', 'string', 'max:255', 'unique:vouchers,code'],
            'date_end' => ['required', 'date'],
            'is_used' => ['nullable', 'boolean'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'in:percentage,fixed'],
            'voucher_type' => ['nullable', 'string'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_discount_value' => ['nullable', 'numeric', 'min:0'],
            'avatar' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => __('please_enter_voucher_code'),
            'code.unique' => __('voucher.code_unique'),
            'date_end.required' => __('please_enter_date_end'),
            'type.required' => __('please_choose_discount_type'),
            'discount_value.required' => __('please_enter_discount_value'),
        ];
    }
}

