<?php

namespace App\Admin\Http\Requests\Voucher;

use App\Admin\Http\Requests\BaseRequest;
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
                $validator->errors()->add('code', 'Mã giảm giá đã tồn tại.');
            }

            if ($this->type == DiscountValueType::Percent->value || $this->type == 'percent') {
                if ((float) $this->discount_value > 100) {
                    $validator->errors()->add('discount_value', 'Giá trị giảm giá theo phần trăm không được vượt quá 100%.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => 'Người dùng không được để trống.',
            'code.required' => 'Mã voucher không được để trống.',
            'code.max' => 'Mã voucher không được vượt quá 50 ký tự.',
            'date_end.required' => 'Ngày kết thúc không được để trống.',
            'date_end.date' => 'Ngày kết thúc không hợp lệ.',
            'date_end.after' => 'Ngày kết thúc phải sau hôm nay.',
            'min_order_amount.min' => 'Số tiền đơn hàng tối thiểu phải lớn hơn 0.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'max_discount_value.gte' => 'Giá trị giảm giá tối đa phải lớn hơn hoặc bằng giá trị giảm giá.',
            'avatar.image' => 'Ảnh đại diện phải là một tập tin hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg, gif.',
            'avatar.max' => 'Ảnh đại diện không được lớn hơn 2MB.',
        ];
    }
}
