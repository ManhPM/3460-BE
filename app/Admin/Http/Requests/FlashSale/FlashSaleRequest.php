<?php

namespace App\Admin\Http\Requests\FlashSale;

use App\Admin\Http\Requests\BaseRequest;
use App\Models\FlashSaleDetail;

class FlashSaleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'unique:App\Models\FlashSale,name'],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',

            'product_id' => ['required', 'array'],
            'product_id.*' => ['required', 'exists:App\Models\Product,id'],
            'product_variation_id' => ['nullable', 'array'],
            'product_variation_id.*' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            'product_variation_flashsale_price' => ['nullable', 'array'],
            'product_variation_flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
            'product_variation_qty' => ['nullable', 'array'],
            'product_variation_qty.*' => ['nullable', 'integer', 'min:1'],
            'qty' => ['nullable', 'array'],
            'qty.*' => ['nullable', 'integer', 'min:1'],
            'flashsale_price' => ['nullable', 'array'],
            'flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\FlashSale,id'],
            'name' => ['required', 'unique:App\Models\FlashSale,name,' . $this->id],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',

            'product_id' => ['required', 'array'],
            'product_id.*' => ['required', 'exists:App\Models\Product,id'],
            'product_variation_id' => ['nullable', 'array'],
            'product_variation_id.*' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            'product_variation_flashsale_price' => ['nullable', 'array'],
            'product_variation_flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
            'product_variation_qty' => ['nullable', 'array'],
            'product_variation_qty.*' => ['nullable', 'integer', 'min:1'],
            'qty' => ['nullable', 'array'],
            'qty.*' => ['nullable', 'integer', 'min:1'],
            'flashsale_price' => ['nullable', 'array'],
            'flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required'],
        ];
    }

    protected function methodGet()
    {
        if ($this->routeIs('admin.flashsale.add_product')) {
            return [
                'product_slug' => ['required', 'exists:App\Models\Product,slug'],
                'product_variation_id' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên chương trình Flash Sale.',
            'name.unique' => 'Tên chương trình Flash Sale đã tồn tại.',
            'start_time.required' => 'Vui lòng nhập thời gian bắt đầu.',
            'start_time.date' => 'Thời gian bắt đầu không hợp lệ.',
            'end_time.required' => 'Vui lòng nhập thời gian kết thúc.',
            'end_time.date' => 'Thời gian kết thúc không hợp lệ.',
            'end_time.after_or_equal' => 'Thời gian kết thúc phải sau hoặc bằng thời gian bắt đầu.',
            'product_id.required' => 'Vui lòng chọn ít nhất một sản phẩm.',
            'product_id.array' => 'Danh sách sản phẩm không hợp lệ.',
            'product_id.*.required' => 'Mỗi sản phẩm được chọn là bắt buộc.',
            'product_id.*.exists' => 'Một hoặc nhiều sản phẩm không tồn tại trong hệ thống.',
            'qty.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'qty.array' => 'Danh sách số lượng không hợp lệ.',
            'flashsale_price.required' => 'Vui lòng nhập giá khuyến mãi cho sản phẩm.',
            'flashsale_price.array' => 'Danh sách giá khuyến mãi không hợp lệ.',
            'product_variation_flashsale_price.array' => 'Danh sách giá khuyến mãi biến thể không hợp lệ.',
            'product_variation_qty.array' => 'Danh sách số lượng biến thể không hợp lệ.',
            'product_variation_qty.*.integer' => 'Số lượng biến thể phải là số nguyên.',
            'product_variation_qty.*.min' => 'Số lượng biến thể phải lớn hơn 0.',
            'qty.*.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'qty.*.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
            'flashsale_price.*.numeric' => 'Giá flash sale phải là số.',
            'flashsale_price.*.min' => 'Giá flash sale phải lớn hơn hoặc bằng 0.',
            'product_variation_flashsale_price.*.numeric' => 'Giá flash sale biến thể phải là số.',
            'product_variation_flashsale_price.*.min' => 'Giá flash sale biến thể phải lớn hơn hoặc bằng 0.',
            'id.required' => 'ID chương trình Flash Sale là bắt buộc khi cập nhật.',
            'id.exists' => 'Chương trình Flash Sale không tồn tại.',
            'product_slug.required' => 'Slug sản phẩm là bắt buộc.',
            'product_slug.exists' => 'Sản phẩm không tồn tại trong hệ thống.',
            'product_variation_id.exists' => 'Biến thể sản phẩm không tồn tại trong hệ thống.',
        ];
    }
}
