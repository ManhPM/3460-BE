<?php

namespace App\Http\Requests\ShoppingCart;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Models\Discount;
use App\Models\Order;

class ApplyDiscountCodeRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'discount_code' => ['nullable', 'exists:discounts,code'],
            'products' => ['nullable'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'points' => ['nullable'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $discount = Discount::where('code', $this->discount_code)->first();
            if ($discount) {
                if ($discount->max_usage <= 0) {
                    $validator->errors()->add('discount_code', __('Mã giảm giá đã hết lượt sử dụng'));
                }
                if ($discount->date_start > now() || $discount->date_end < now()) {
                    $validator->errors()->add('discount_code', __('Mã giảm giá đã hết hạn hoặc chưa có hiệu lực.'));
                }
            }
            if (auth('web')->check() && $discount) {
                $orders = Order::where('user_id', auth('web')->id())
                    ->where('status', '!=', OrderStatus::Cancelled)
                    ->where('discount_code', $discount->code);

                if ($orders->count() >= $discount->max_usage_per_user) {
                    $validator->errors()->add('discount_code', __('Bạn đã sử dụng hết số lượt mã giảm giá cho phép.'));
                }
            }
        });
    }

    public function messages()
    {
        return [
            'discount_code.exists' => __('Mã giảm giá không tồn tại.'),
        ];
    }
}
