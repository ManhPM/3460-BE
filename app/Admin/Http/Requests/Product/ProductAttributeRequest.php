<?php

namespace App\Admin\Http\Requests\Product;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Product\ProductType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Validation\Rules\Enum;

class ProductAttributeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'attribute_id' => ['required', 'exists:App\Models\Attribute,id'],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'name' => ['required', 'string'],
            'desc' => ['nullable'],
            'categories_id.*' => ['required', 'exists:App\Models\Category,id'],
            'avatar' => ['required'],
            'type' => ['required', new Enum(ProductType::class)],
            'price' => ['nullable', 'numeric'],
            'promotion_price' => ['nullable', 'numeric'],
            'in_stock' => ['required', 'boolean'],
            'gallery' => ['nullable'],
            'toppings_id.*' => ['nullable', 'exists:App\Models\Topping,id'],

        ];
    }

    public function messages()
    {
        return [
            'attribute_id.required' => 'Trường attribute_id là bắt buộc.',
            'attribute_id.exists' => 'Attribute không tồn tại.',

            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi.',

            'categories_id.*.required' => 'Danh mục là bắt buộc.',
            'categories_id.*.exists' => 'Danh mục không hợp lệ.',

            'avatar.required' => 'Ảnh đại diện sản phẩm là bắt buộc.',

            'type.required' => 'Loại sản phẩm là bắt buộc.',
            'type.enum' => 'Loại sản phẩm không hợp lệ.',

            'price.numeric' => 'Giá sản phẩm phải là số.',
            'promotion_price.numeric' => 'Giá khuyến mãi phải là số.',

            'in_stock.required' => 'Trạng thái tồn kho là bắt buộc.',
            'in_stock.boolean' => 'Trạng thái tồn kho không hợp lệ.',

            'toppings_id.*.exists' => 'Topping không hợp lệ.',
        ];
    }
}
