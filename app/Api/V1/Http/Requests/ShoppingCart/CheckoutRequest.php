<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Voucher\VoucherType;
use App\Models\Discount;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\Voucher;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CheckoutRequest extends BaseRequest
{
    protected $repository;
    public function __construct(
        SettingRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    protected function methodPost()
    {
        return [
            'id' => ['required'],
            'discount_code' => ['nullable', 'exists:App\Models\Discount,code'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
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
            'order.bank_id' => ['nullable', 'exists:banks,id'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
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
