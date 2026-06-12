<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
            'attribute_id.required' => __('please_enter_attribute_id'),
            'attribute_id.exists' => __('attribute.not_exists'),

            'name.required' => __('please_enter_product_name'),
            'name.string' => __('product.name_string'),

            'categories_id.*.required' => __('category.required'),
            'categories_id.*.exists' => __('category.not_exists'),

            'avatar.required' => __('please_enter_product_avatar'),

            'type.required' => __('please_choose_product_type'),
            'type.enum' => __('product.type_invalid'),

            'price.numeric' => __('product.price_numeric'),
            'promotion_price.numeric' => __('product.promotion_price_numeric'),

            'in_stock.required' => __('product.in_stock_required'),
            'in_stock.boolean' => __('product.in_stock_boolean'),

            'toppings_id.*.exists' => __('topping.not_exists'),
        ];
    }
}
