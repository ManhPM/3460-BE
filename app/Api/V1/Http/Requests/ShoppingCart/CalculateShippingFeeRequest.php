<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Enums\Voucher\VoucherType;
use App\Models\Discount;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\Voucher;

class CalculateShippingFeeRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'id' => ['required'],
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'discount_code' => ['nullable', 'exists:App\Models\Discount,code'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
        ];
    }
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
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
                        $validator->errors()->add('code', __('discount.max_usage_per_user_exceeded'));
                    }
                }
            }
            if (auth()->id()) {
                $this->validateVoucher($validator, $this->voucher_shipping_id, 'shipping');
                $this->validateVoucher($validator, $this->voucher_product_id, 'product');
                $userId = auth()->id();
                $cartIds = $this->id;

                // Get the cart IDs that exist in the database
                $existingCartIds = ShoppingCart::whereIn('id', $cartIds)->pluck('id')->all();

                // Identify the cart IDs that do not exist in the database
                $nonExistentCartIds = array_diff($cartIds, $existingCartIds);

                if (!empty($nonExistentCartIds)) {
                    $validator->errors()->add('id', __('shopping_cart.some_not_exists'));
                }

                if (isset($this->points) && $this->points > auth()->user()->points) {
                    $validator->errors()->add('points', __('shopping_cart.insufficient_points'));
                }
            }
        });
    }

    private function validateVoucher($validator, $voucherId, $type = 'shipping')
    {
        if (isset($voucherId)) {
            $voucher = Voucher::find($voucherId);

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

    public function messages()
    {
        return [
            'id.required' => __('please_choose_cart_item'),
            'province_id.required' => __('please_choose_province'),
            'province_id.exists' => __('province_not_exists'),
            'ward_id.required' => __('please_choose_ward'),
            'ward_id.exists' => __('ward_not_exists'),
            'discount_code.exists' => __('discount.not_exists'),
            'voucher_shipping_id.exists' => __('voucher.shipping_not_exists'),
            'voucher_product_id.exists' => __('voucher.product_not_exists'),
        ];
    }
}
