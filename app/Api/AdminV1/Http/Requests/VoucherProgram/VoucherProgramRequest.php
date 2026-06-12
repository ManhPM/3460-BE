<?php

namespace App\Api\AdminV1\Http\Requests\VoucherProgram;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Validation\Rules\Enum;

class VoucherProgramRequest extends BaseRequest
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
            'name' => ['required'],
            'expiration_days' => ['required', 'integer', 'min:1'],
            'type' => ['required', new Enum(DiscountValueType::class)],
            'voucher_type' => ['required', new Enum(VoucherType::class)],
            'min_order_amount' => ['required', 'numeric', 'min:1'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'max_discount_value' => ['required', 'numeric', 'min:1'],
            'avatar' => ['required'],
            'qty' => ['required', 'numeric', 'min:1'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:voucher_programs,id'],
            'name' => ['required'],
            'expiration_days' => ['required', 'integer', 'min:1'],
            'type' => ['required', new Enum(DiscountValueType::class)],
            'voucher_type' => ['required', new Enum(VoucherType::class)],
            'min_order_amount' => ['required', 'numeric', 'min:1'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'max_discount_value' => ['required', 'numeric', 'min:1'],
            'avatar' => ['required'],
            'qty' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('please_enter_voucher_program_name'),
            'expiration_days.required' => __('please_enter_expiration_days'),
            'expiration_days.integer' => __('voucher_program.expiration_days_integer'),
            'expiration_days.min' => __('voucher_program.expiration_days_min'),
            'min_order_amount.min' => __('voucher_program.min_order_amount_min'),
            'discount_value.min' => __('voucher_program.discount_value_min'),
            'max_discount_value.min' => __('voucher_program.max_discount_value_min'),
            'qty.min' => __('voucher_program.qty_min'),
        ];
    }
}
