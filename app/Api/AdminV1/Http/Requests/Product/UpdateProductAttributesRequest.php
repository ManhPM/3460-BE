<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateProductAttributesRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'product_attribute.attribute_id' => ['required', 'array'],
            'product_attribute.attribute_id.*' => ['required', 'exists:App\Models\Attribute,id'],
            'product_attribute.attribute_variation_id' => ['required', 'array'],
            'product_attribute.attribute_variation_id.*' => ['required', 'array'],
            'product_attribute.attribute_variation_id.*.*' => ['required', 'exists:App\Models\AttributeVariation,id'],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => __('please_enter_product_id'),
            'product_id.exists' => __('product_id_not_exists'),
            'product_attribute.attribute_id.required' => __('please_choose_attribute'),
            'product_attribute.attribute_id.array' => __('attribute.array'),
            'product_attribute.attribute_id.*.required' => __('please_enter_attribute_id'),
            'product_attribute.attribute_id.*.exists' => __('attribute.not_exists'),
            'product_attribute.attribute_variation_id.required' => __('please_choose_attribute_variation'),
            'product_attribute.attribute_variation_id.array' => __('attribute_variation.array'),
            'product_attribute.attribute_variation_id.*.required' => __('attribute_variation.array'),
            'product_attribute.attribute_variation_id.*.array' => __('attribute_variation.array'),
            'product_attribute.attribute_variation_id.*.*.required' => __('please_enter_attribute_variation_id'),
            'product_attribute.attribute_variation_id.*.*.exists' => __('attribute_variation.not_exists'),
        ];
    }
}

