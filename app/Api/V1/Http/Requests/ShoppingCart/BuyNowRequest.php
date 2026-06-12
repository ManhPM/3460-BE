<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Voucher\VoucherType;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\Voucher;
use Illuminate\Validation\Rules\Enum;

class BuyNowRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        $product = Product::findOrFail($this->product_id);
        $base = [
            'discount_code' => ['nullable', 'exists:App\Models\Discount,code'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
            'affiliate_code' => ['nullable', 'string', 'exists:users,affiliate_code'],
            'order.payment_method' => ['required', new Enum(PaymentMethod::class)],
            'order.payment_image' => ['nullable'],
            'order.email' => ['nullable'],
            'order.admin_id' => ['required', 'exists:admins,id'],
            'order.province_id' => ['required', 'exists:App\Models\Province,id'],
            'order.ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'order.fullname' => ['required'],
            'points' => ['nullable'],
            'order.address' => ['required'],
            'order.phone' => ['required'],
            'order.note' => ['nullable'],
            'order.bank_id' => ['nullable']
        ];
        if ($product && $product->isSimple()) {
            return array_merge([
                'product_id' => ['required', 'exists:App\Models\Product,id'],
                'qty' => ['required', 'integer', 'min:1'],
            ], $base);
        } else {
            return array_merge([
                'product_id' => ['required', 'exists:App\Models\Product,id'],
                'variation_id' => ['required'],
                'qty' => ['required', 'integer', 'min:1'],
            ], $base);
        }
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('affiliate_code')) {
                $affiliateUser = \App\Models\User::where('affiliate_code', $this->input('affiliate_code'))->first();
                if ($affiliateUser && auth('sanctum')->check() && auth('sanctum')->id() === $affiliateUser->id) {
                    $validator->errors()->add('affiliate_code', __('Mã giới thiệu không thể là của chính bạn.'));
                }
            }

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
            $product = Product::find($this->product_id);

            if ($product && !$product->isSimple()) {
                $variation_id = $this->variation_id;
                $isExist = $product->productVariations()->where('id', $variation_id)->first();
                if (!$isExist) {
                    $validator->errors()->add('variation_id', __('product_variation_id_not_exists'));
                }
            }

            if (auth()->id()) {
                $this->validateVoucher($validator, $this->voucher_shipping_id, 'shipping');
                $this->validateVoucher($validator, $this->voucher_product_id, 'product');
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
