<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductVariationAction;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

class ProductVariationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        if ($this->routeIs('admin.product.variation.check') || $this->routeIs('admin.product.variation.create')) {
            $this->validate['product_attribute.attribute_id'] = ['required', 'array'];
            $this->validate['product_attribute.attribute_id.*'] = ['required', 'exists:App\Models\Attribute,id'];
            $this->validate['product_attribute.attribute_variation_id'] = ['required', 'array'];
            $this->validate['product_attribute.attribute_variation_id.*'] = ['required', 'array'];
            $this->validate['product_attribute.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
            if ($this->routeIs('admin.product.variation.create')) {
                $this->validate['variation_action'] = ['required', new EnumValue(ProductVariationAction::class, false)];
            }
        }
        return $this->validate;
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

    protected function failedValidation(Validator $validator)
    {
        if ($this->routeIs('admin.product.variation.check')) {
            $errors = (new ValidationException($validator))->errors();
            $viewError = view('admin.products.data.partials.no-variation')->render();
            throw new HttpResponseException(
                response()->json([
                    'errors' => $errors,
                    'viewError' => $viewError
                ], 422)
            );
        }
    }

    public function messages()
    {
        return [
            'product_attribute.attribute_id.required' => __('please_choose_attribute'),
            'product_attribute.attribute_id.array' => __('please_choose_attribute'),
            'product_attribute.attribute_id.*.required' => __('please_choose_attribute'),
            'product_attribute.attribute_id.*.exists' => __('attribute.not_exists'),

            'product_attribute.attribute_variation_id.required' => __('please_choose_attribute_variation'),
            'product_attribute.attribute_variation_id.array' => __('please_choose_attribute_variation'),
            'product_attribute.attribute_variation_id.*.required' => __('please_choose_attribute_variation'),
            'product_attribute.attribute_variation_id.*.*.required' => __('please_choose_attribute_variation'),
            'product_attribute.attribute_variation_id.*.*.exists' => __('attribute_variation.not_exists'),

            'variation_action.required' => __('product.variation_action_required'),
            'variation_action.enum_value' => __('product.variation_action_invalid'),

            'name.required' => __('product.variation_name_required'),
            'name.string' => __('product.variation_name_string'),

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
