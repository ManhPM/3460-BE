<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateProductVariationsRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'products_variations.id' => ['required', 'array'],
            'products_variations.id.*' => ['required', 'integer'],
            'products_variations.price' => ['required', 'array'],
            'products_variations.price.*' => ['required', 'numeric'],
            'products_variations.promotion_price' => ['required', 'array'],
            'products_variations.promotion_price.*' => ['required', 'numeric'],
            'products_variations.qty' => ['nullable', 'array'],
            'products_variations.qty.*' => ['nullable', 'integer', 'min:0'],
            'products_variations.image' => ['nullable', 'array'],
            'products_variations.image.*' => ['nullable', 'string'],
            'products_variations.attribute_variation_id' => ['required', 'array'],
            'products_variations.attribute_variation_id.*' => ['required', 'array'],
            'products_variations.attribute_variation_id.*.*' => ['required', 'exists:App\Models\AttributeVariation,id'],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => __('please_enter_product_id'),
            'product_id.exists' => __('product_id_not_exists'),
            'products_variations.id.required' => __('please_enter_variation_id'),
            'products_variations.id.array' => __('product.variation_id_array'),
            'products_variations.id.*.required' => __('please_enter_variation_id'),
            'products_variations.id.*.integer' => __('product.variation_id_integer'),
            'products_variations.price.required' => __('please_enter_variation_price'),
            'products_variations.price.array' => __('product.variation_price_array'),
            'products_variations.price.*.required' => __('please_enter_variation_price'),
            'products_variations.price.*.numeric' => __('product.variation_price_numeric'),
            'products_variations.promotion_price.required' => __('please_enter_variation_promotion_price'),
            'products_variations.promotion_price.array' => __('product.variation_promotion_price_array'),
            'products_variations.promotion_price.*.required' => __('please_enter_variation_promotion_price'),
            'products_variations.promotion_price.*.numeric' => __('product.variation_promotion_price_numeric'),
            'products_variations.qty.array' => __('product.variation_qty_array'),
            'products_variations.qty.*.integer' => __('quantity_must_be_integer'),
            'products_variations.qty.*.min' => __('quantity_min_value'),
            'products_variations.image.array' => __('product.variation_image_array'),
            'products_variations.image.*.string' => __('product.variation_image_string'),
            'products_variations.attribute_variation_id.required' => __('please_choose_attribute_variation'),
            'products_variations.attribute_variation_id.array' => __('attribute_variation.array'),
            'products_variations.attribute_variation_id.*.required' => __('attribute_variation.array'),
            'products_variations.attribute_variation_id.*.array' => __('attribute_variation.array'),
            'products_variations.attribute_variation_id.*.*.required' => __('please_enter_attribute_variation_id'),
            'products_variations.attribute_variation_id.*.*.exists' => __('attribute_variation.not_exists'),
        ];
    }
}

