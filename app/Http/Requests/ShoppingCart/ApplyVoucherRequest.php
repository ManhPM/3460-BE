<?php

namespace App\Http\Requests\ShoppingCart;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Voucher;

class ApplyVoucherRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'code' => ['nullable', 'exists:discounts,code'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
            'cart_id' => ['nullable'],
            'qty' => ['nullable'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateVoucher($validator, $this->voucher_shipping_id);
            $this->validateVoucher($validator, $this->voucher_product_id);
            $discount = Discount::where('code', $this->code)->first();
            if ($discount) {
                if ($discount->max_usage <= 0) {
                    $validator->errors()->add('code', __('Mã giảm giá đã hết lượt sử dụng'));
                }
                if ($discount->date_start > now() || $discount->date_end < now()) {
                    $validator->errors()->add('code', __('Mã giảm giá không hợp lệ'));
                }
            }
            if (auth('web')->user() && $discount) {
                $orders = Order::where('user_id', auth('web')->id())
                    ->where('status', '!=', OrderStatus::Cancelled)
                    ->where('discount_code', $discount->code);

                if ($orders->count() >= $discount->max_usage_per_user) {
                    $validator->errors()->add('code', __('Bạn đã sử dụng hết số lượt mã giảm giá cho phép.'));
                }
            }
        });
    }

    private function validateVoucher($validator, $voucherId)
    {
        if (isset($voucherId)) {
            $voucher = Voucher::find($voucherId);

            if (!$voucher) {
                $validator->errors()->add('voucher_id', __('Mã giảm giá không tồn tại.'));
                return;
            }

            if ($voucher->date_start && $voucher->date_start > now()) {
                $validator->errors()->add('voucher_id', __('Mã giảm giá chưa đến thời gian sử dụng.'));
            }

            if ($voucher->date_end && $voucher->date_end < now()->startOfDay()) {
                $validator->errors()->add('voucher_id', __('Mã giảm giá đã hết hạn.'));
            }

            if ($voucher->is_used) {
                $validator->errors()->add('voucher_id', __('Mã giảm giá đã được sử dụng.'));
            }
        }
    }

    public function messages()
    {
        return [
            'voucher_shipping_id.exists' => __('Mã giảm giá vận chuyển không tồn tại.'),
            'voucher_product_id.exists' => __('Mã giảm giá sản phẩm không tồn tại.'),
            'code.exists' => __('Mã giảm giá không tồn tại.'),
        ];
    }
}
