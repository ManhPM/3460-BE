<?php

namespace App\Http\Requests\ShoppingCart;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\PaymentMethod;
use Illuminate\Validation\Rule;
use App\Models\Discount;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\Voucher;
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
            'qty' => ['required'],
            'isBuyNow' => ['nullable'],
            'discount_code' => ['nullable', 'exists:App\Models\Discount,code'],
            'voucher_shipping_id' => ['nullable', 'exists:vouchers,id'],
            'voucher_product_id' => ['nullable', 'exists:vouchers,id'],
            'order.payment_method' => ['required', new Enum(PaymentMethod::class)],
            'order.email' => ['nullable', 'email'],
            'order.payment_image' => ['nullable'],
            'points' => ['nullable'],
            'order.admin_id' => ['nullable', 'exists:admins,id'],
            'order.province_id' => ['required', 'exists:App\Models\Province,id'],
            'order.ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'order.fullname' => ['required'],
            'order.address' => ['required'],
            'order.phone' => ['required'],
            'order.note' => ['nullable'],
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
            $this->validateVoucher($validator, $this->voucher_shipping_id);
            $this->validateVoucher($validator, $this->voucher_product_id);
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
            'discount_code.exists' => __('Mã giảm giá không tồn tại.'),
            'order.payment_method.required' => __('Vui lòng chọn phương thức thanh toán.'),
            'order.province_id.required' => __('Vui lòng nhập đầy đủ thông tin giao hàng.'),
            'order.ward_id.required' => __('Vui lòng nhập đầy đủ thông tin giao hàng.'),
            'order.address.required' => __('Vui lòng nhập đầy đủ thông tin giao hàng.'),
            'order.fullname.required' => __('Vui lòng nhập đầy đủ thông tin giao hàng.'),
            'order.phone.required' => __('Vui lòng nhập đầy đủ thông tin giao hàng.'),
        ];
    }
}
