<?php

namespace App\Api\AdminV1\Http\Requests\Discount;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreDiscountRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:discounts,code'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after:date_start'],
            'max_usage' => ['nullable', 'integer', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_discount_value' => ['nullable', 'numeric', 'min:0'],
            'max_usage_per_user' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => __('please_enter_discount_code'),
            'code.unique' => __('discount.code_exists'),
            'date_start.required' => __('please_enter_date_start'),
            'date_end.required' => __('please_enter_date_end'),
            'date_end.after' => __('discount.date_end_after_start'),
            'type.required' => __('please_choose_discount_type'),
            'discount_value.required' => __('please_enter_discount_value'),
        ];
    }
}

