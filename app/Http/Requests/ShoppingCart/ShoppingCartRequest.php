<?php

namespace App\Http\Requests\ShoppingCart;

use App\Admin\Http\Requests\BaseRequest;

class ShoppingCartRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'product_variation_id' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'ID sản phẩm là bắt buộc.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'product_variation_id.exists' => 'Phiên bản sản phẩm không tồn tại.',
            'qty.required' => 'Số lượng là bắt buộc.',
            'qty.integer' => 'Số lượng phải là số nguyên.',
            'qty.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
        ];
    }
}
