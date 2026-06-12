<?php

namespace App\Admin\Http\Requests\Order;

use App\Admin\Http\Requests\BaseRequest;
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
            'order.total' => ['nullable'],
            'order.payment_method' => ['nullable'],
            'order.payment_status' => ['nullable'],
            'order.discount_value' => ['nullable'],
            'order_detail.unit_price' => ['nullable', 'array'],
            'order_detail.unit_price.*' => ['nullable'],
            'order_detail.product_slug' => ['required', 'array'],
            'order_detail.product_slug.*' => ['required', 'exists:App\Models\Product,slug'],
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
            'order.id.required' => 'Vui lòng cung cấp ID đơn hàng.',
            'order.id.exists' => 'Đơn hàng không tồn tại.',
            'order.user_id.required' => 'Vui lòng chọn người dùng.',
            'order.user_id.exists' => 'Người dùng không tồn tại.',
            'order.admin_id.exists' => 'Chi nhánh không tồn tại.',
            'order.ward_id.required' => 'Vui lòng chọn Thành phố/Khu vực.',
            'order.ward_id.exists' => 'Thành phố/Khu vực không tồn tại.',
            'order.province_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'order.province_id.exists' => 'Tỉnh/thành phố không tồn tại.',
            'order.fullname.required' => 'Vui lòng nhập họ và tên.',
            'order.phone.required' => 'Vui lòng nhập số điện thoại.',

            'order.email.required' => 'Vui lòng nhập email.',
            'order.email.email' => 'Email không hợp lệ.',
            'order_detail.product_slug.*.required' => 'Vui lòng chọn sản phẩm.',
            'order_detail.product_slug.*.exists' => 'Sản phẩm không tồn tại.',
            'order_detail.product_qty.*.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'order_detail.product_qty.*.integer' => 'Số lượng phải là số nguyên.',
            'order_detail.product_qty.*.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
        ];
    }
}
