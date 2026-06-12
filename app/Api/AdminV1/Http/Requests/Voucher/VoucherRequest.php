<?php

namespace App\Api\AdminV1\Http\Requests\Voucher;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class VoucherRequest extends BaseRequest
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
            'user_id' => ['required', 'exists:App\Models\User,id'],
            'code' => ['required'],
            'date_end' => ['required'],
            'is_used' => ['required'],
            'type' => ['required', new Enum(DiscountValueType::class)],
            'voucher_type' => ['required', new Enum(VoucherType::class)],
            'min_order_amount' => ['required', 'numeric', 'min:1'],
            'discount_value' => ['required'],
            'max_discount_value' => ['required', 'numeric', 'min:1'],
            'avatar' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Voucher,id'],
            'user_id' => ['required', 'exists:App\Models\User,id'],
            'code' => ['required'],
            'date_end' => ['required'],
            'is_used' => ['required'],
            'type' => ['required', new Enum(DiscountValueType::class)],
            'voucher_type' => ['required', new Enum(VoucherType::class)],
            'min_order_amount' => ['required', 'numeric', 'min: 1'],
            'discount_value' => ['required'],
            'max_discount_value' => ['required', 'numeric', 'min: 1'],
            'avatar' => ['required'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $code = $this->code;

            $discountExists = DB::table('discounts')
                ->where('code', $code)
                ->exists();

            $voucherExists = DB::table('vouchers')
                ->where('code', $code)->first();

            if ($discountExists || ($voucherExists && $voucherExists->id != $this->id)) {
                $validator->errors()->add('code', __('voucher.code_exists'));
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => __('please_choose_user'),
            'code.required' => __('please_enter_voucher_code'),
            'code.max' => __('voucher.code_max'),
            'date_end.required' => __('please_enter_date_end'),
            'date_end.date' => __('voucher.date_end_invalid'),
            'date_end.after' => __('voucher.date_end_after_today'),
            'min_order_amount.min' => __('voucher.min_order_amount_min'),
            'discount_value.min' => __('voucher.discount_value_min'),
            'max_discount_value.gte' => __('voucher.max_discount_value_gte'),
            'avatar.image' => __('voucher.avatar_image'),
            'avatar.mimes' => __('voucher.avatar_mimes'),
            'avatar.max' => __('voucher.avatar_max'),
        ];
    }
}
