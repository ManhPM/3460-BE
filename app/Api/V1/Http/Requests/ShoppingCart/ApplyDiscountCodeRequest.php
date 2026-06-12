<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Enums\Voucher\VoucherType;
use App\Models\Discount;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\Voucher;

class ApplyDiscountCodeRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'discount_code' => ['nullable', 'exists:discounts,code'],
            'products' => ['required'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
            'province_id' => ['nullable', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'points' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $provinceId = $this->input('province_id');
            $wardId = $this->input('ward_id');

            // Kiểm tra nếu bất kỳ một trong hai giá trị tồn tại thì cả hai phải có mặt
            if (($provinceId || $wardId) && (!$provinceId || !$wardId)) {
                $validator->errors()->add('province_id', __('user.choose_full_address'));
            }

            $this->validateVoucher($validator, $this->voucher_shipping_id, 'shipping');
            $this->validateVoucher($validator, $this->voucher_product_id, 'product');
            $discount = Discount::where('code', $this->discount_code)->first();
            if ($discount) {
                if ($discount->max_usage <= 0) {
                    $validator->errors()->add('discount_code', __('discount.max_usage_exceeded'));
                }
                if (auth()->id()) {
                    $orders = Order::where('user_id', auth()->id())
                        ->where('status', '!=', OrderStatus::Cancelled)
                        ->where('discount_code', $discount->code);

                    if ($orders->count() >= $discount->max_usage_per_user) {
                        $validator->errors()->add('discount_code', __('discount.max_usage_per_user_exceeded'));
                    }
                }
            }
        });
    }

    private function validateVoucher($validator, $voucherId, $type = 'shipping')
    {
        if (isset($voucherId)) {
            $voucher = Voucher::find($voucherId);

            if ($voucher) {
                if ($voucher->date_end < now()->startOfDay()) {
                    $validator->errors()->add('voucher_shipping_id', __('voucher.expired'));
                }

                if ($voucher->is_used) {
                    $validator->errors()->add('voucher_shipping_id', __('voucher.already_used'));
                }

                if ($voucher && $voucher->type == VoucherType::Product && $type == 'shipping') {
                    $validator->errors()->add('voucher_shipping_id', __('discount.invalid'));
                }

                if ($voucher && $voucher->type == VoucherType::Shipping && $type == 'product') {
                    $validator->errors()->add('voucher_shipping_id', __('discount.invalid'));
                }
            }
        }
    }

    public function messages()
    {
        return [
            'voucher_shipping_id.exists' => __('voucher.shipping_not_exists'),
            'voucher_product_id.exists' => __('voucher.product_not_exists'),
            'discount_code.exists' => __('discount.not_exists'),
        ];
    }
}
