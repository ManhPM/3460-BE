<?php

namespace App\Admin\Http\Requests\VoucherProgram;

use App\Admin\Http\Requests\BaseRequest;
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
            'name.required' => 'Tên chương trình không được để trống.',
            'expiration_days.required' => 'Số ngày hết hạn không được để trống.',
            'expiration_days.integer' => 'Số ngày hết hạn phải là số nguyên.',
            'expiration_days.min' => 'Số ngày hết hạn phải lớn hơn 0.',
            'min_order_amount.min' => 'Giá trị đơn hàng tối thiểu phải lớn hơn 0.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'max_discount_value.min' => 'Mức giảm giá tối đa phải lớn hơn 0.',
            'qty.min' => 'Số lượng voucher phải lớn hơn 0.',
        ];
    }
}
