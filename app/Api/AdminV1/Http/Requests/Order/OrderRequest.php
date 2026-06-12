<?php

namespace App\Api\AdminV1\Http\Requests\Order;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use Illuminate\Validation\Rules\Enum;

class OrderRequest extends BaseRequest
{
    public function methodPost()
    {
        return [
            'order.user_id' => ['required', 'exists:App\Models\User,id'],
            'order.admin_id' => ['nullable', 'exists:App\Models\Admin,id'],
            'order.ward_id' => ['required', 'exists:App\Models\Ward,id'],
            'order.province_id' => ['required', 'exists:App\Models\Province,id'],
            'discount_id' => ['nullable', 'exists:App\Models\Discount,id'],
            'order.fullname' => ['required'],
            'order.phone' => ['required'],
            'order.email' => ['required', 'email'],
            'order.address' => ['nullable'],
            'order.note' => ['nullable'],
            'order.shipping_fee' => ['nullable'],
            'order.total' => ['nullable'],
            'order.payment_method' => ['nullable'],
            'order.payment_status' => ['nullable'],
            'order.discount_value' => ['nullable'],
            'order_detail.unit_price' => ['nullable', 'array'],
            'order_detail.unit_price.*' => ['nullable'],
            'order_detail.product_id' => ['required', 'array'],
            'order_detail.product_id.*' => ['required', 'exists:App\Models\Product,id'],
            'order_detail.product_variation_id' => ['required', 'array'],
            'order_detail.product_variation_id.*' => ['required'],
            'order_detail.product_qty' => ['required', 'array'],
            'order_detail.product_qty.*' => ['required', 'integer', 'min:1'],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPut()
    {
        return [
            'order.id' => ['required', 'exists:App\Models\Order,id'],
            'order.status' => ['required', new Enum(OrderStatus::class)],
            'order.note' => ['nullable'],
            'order.total' => ['nullable'],
            'order.payment_status' => ['nullable'],
            'order.discount_value' => ['nullable'],
            'order_detail.id' => ['nullable', 'array'],
            'order_detail.product_slug' => ['nullable', 'array'],
            'order_detail.unit_price' => ['nullable', 'array'],
            'order_detail.unit_price.*' => ['nullable'],
            'order_detail.product_slug.*' => ['nullable', 'exists:App\Models\Product,slug'],
            'order_detail.product_variation_id' => ['nullable', 'array'],
            'order_detail.product_variation_id.*' => ['nullable'],
            'order_detail.product_qty' => ['nullable', 'array'],
            'order_detail.product_qty.*' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function methodGet()
    {
        if ($this->routeIs('admin.order.render_info_shipping')) {
            return [
                'user_id' => ['required', 'exists:App\Models\User,id']
            ];
        } elseif ($this->routeIs('admin.order.add_product')) {
            return [
                'product_slug' => ['required', 'exists:App\Models\Product,slug'],
                'product_variation_id' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            ];
        } elseif ($this->routeIs('admin.order.calculate_total_before_save_order')) {
            return [
                'order.user_id' => ['nullable', 'exists:App\Models\User,id'],
                'order_detail.product_slug.*' => ['required', 'exists:App\Models\Product,slug'],
                'order_detail.product_variation_id.*' => ['required'],
                'order_detail.product_qty.*' => ['required', 'integer', 'min:1'],
                'order.discount_id' => ['nullable', 'exists:App\Models\Discount,id'],
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'order.id.required' => __('please_provide_order_id'),
            'order.id.exists' => __('order_not_exists'),
            'order.user_id.required' => __('please_choose_user'),
            'order.user_id.exists' => __('user_not_found'),
            'order.admin_id.exists' => __('admin_id_not_exists'),
            'order.ward_id.required' => __('please_choose_ward'),
            'order.ward_id.exists' => __('ward_not_exists'),
            'order.province_id.required' => __('please_choose_province'),
            'order.province_id.exists' => __('province_not_exists'),
            'order.fullname.required' => __('please_enter_fullname'),
            'order.phone.required' => __('please_enter_phone'),

            'order.email.required' => __('please_enter_email'),
            'order.email.email' => __('order_customer_email_invalid'),
            'order_detail.product_id.*.required' => __('please_choose_product'),
            'order_detail.product_id.*.exists' => __('product_id_not_exists'),
            'order_detail.product_qty.*.required' => __('please_enter_product_quantity'),
            'order_detail.product_qty.*.integer' => __('quantity_must_be_integer'),
            'order_detail.product_qty.*.min' => __('quantity_min_value'),
        ];
    }
}
