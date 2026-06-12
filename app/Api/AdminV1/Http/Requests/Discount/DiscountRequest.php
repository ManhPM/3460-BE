<?php

namespace App\Api\AdminV1\Http\Requests\Discount;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountRequest extends BaseRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'discount_value' => $this->cleanDiscountValue($this->input('discount_value')),
        ]);
    }

    private function cleanDiscountValue($value)
    {
        return is_string($value) ? preg_replace('/( VND| %|,)/', '', $value) : $value;
    }

    protected function methodPost(): array
    {
        return [
            'code' => ['required'],
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'max_usage' => ['required', 'numeric', 'min: 1'],
            'min_order_amount' => ['required', 'numeric', 'min: 1'],
            'discount_value' => ['required'],
            'max_discount_value' => ['required', 'numeric', 'min: 1'],
            'max_usage_per_user' => ['required', 'numeric', 'min: 1'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Discount,id'],
            'code' => ['required'],
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'max_usage' => ['required', 'numeric', 'min: 1'],
            'min_order_amount' => ['required', 'numeric', 'min: 1'],
            'discount_value' => ['required'],
            'max_discount_value' => ['required', 'numeric', 'min: 1'],
            'max_usage_per_user' => ['required', 'numeric', 'min: 1'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $code = $this->code;

            $discountExist = Discount::where('code', $code)->first();
            if ($this->isMethod('put')) {
                if ($discountExist && $discountExist->id != $this->id) {
                    $validator->errors()->add('code', __('discount.code_exists'));
                }
            }
            if ($this->isMethod('post')) {
                if ($discountExist) {
                    $validator->errors()->add('code', __('discount.code_exists'));
                }
            }

            $voucherExists = DB::table('vouchers')
                ->where('code', $code)
                ->exists();

            if ($voucherExists) {
                $validator->errors()->add('code', __('discount.code_exists'));
            }
        });
    }

    public function messages()
    {
        return [
            'code.required' => __('please_enter_discount_code'),
            'date_start.required' => __('please_enter_date_start'),
            'date_end.required' => __('please_enter_date_end'),
            'date_end.after_or_equal' => __('discount.date_end_after_start'),
            'max_usage.required' => __('please_enter_max_usage'),
            'max_usage.numeric' => __('discount.max_usage_numeric'),
            'max_usage.min' => __('discount.max_usage_min'),
            'min_order_amount.required' => __('please_enter_min_order_amount'),
            'min_order_amount.numeric' => __('discount.min_order_amount_numeric'),
            'min_order_amount.min' => __('discount.min_order_amount_min'),
            'discount_value.required' => __('please_enter_discount_value'),
            'max_discount_value.required' => __('please_enter_max_discount_value'),
            'max_discount_value.numeric' => __('discount.max_discount_value_numeric'),
            'max_discount_value.min' => __('discount.max_discount_value_min'),
            'max_usage_per_user.required' => __('please_enter_max_usage_per_user'),
            'max_usage_per_user.numeric' => __('discount.max_usage_per_user_numeric'),
            'max_usage_per_user.min' => __('discount.max_usage_per_user_min'),
            'id.required' => __('please_enter_discount_id'),
            'id.exists' => __('discount.not_exists'),
        ];
    }
}
