<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\DefaultActiveStatus;
use App\Enums\Product\ProductType;
use Illuminate\Validation\Rules\Enum;

class ProductRequest extends BaseRequest
{
    public function methodGet()
    {
        return [
            'id' => ['required', 'exists:App\Models\Product,id'],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        $this->validate = [
            'product.name' => ['required', 'string'],
            'product.desc' => ['nullable'],
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\Category,id'],
            'product.avatar' => ['required'],
            'product.price' => ['nullable', 'numeric'],
            'product.promotion_price' => ['nullable', 'numeric'],
            'product.type' => ['nullable', new Enum(ProductType::class)],
            'product.is_featured' => ['required'],
            'product.is_contact_price' => ['required'],
            'product.gallery' => ['nullable'],
        ];
        if ($this->input('product.type') == ProductType::Simple->value) {
            $this->validate['product.price'] = ['required', 'numeric', 'min:1'];
            $this->validate['product.promotion_price'] = ['required', 'numeric', 'min:1'];
        } elseif ($this->input('product.type') == ProductType::Variable->value) {
            // Chỉ validate product_attribute nếu có gửi lên
            if ($this->has('product_attribute')) {
                if ($this->has('product_attribute.attribute_id')) {
                    $this->validate['product_attribute.attribute_id'] = ['required', 'array'];
                    $this->validate['product_attribute.attribute_id.*'] = ['required', 'exists:App\Models\Attribute,id'];
                }
                if ($this->has('product_attribute.attribute_variation_id')) {
                    $this->validate['product_attribute.attribute_variation_id'] = ['required', 'array'];
                    $this->validate['product_attribute.attribute_variation_id.*'] = ['required', 'array'];
                    $this->validate['product_attribute.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
                }
            }
            // Chỉ validate products_variations nếu có gửi lên
            if ($this->has('products_variations')) {
                if ($this->input('products_variations.attribute_variation_id') && count($this->input('products_variations.attribute_variation_id')) > 0) {
                    $this->validate['products_variations.attribute_variation_id'] = ['required', 'array'];
                    $this->validate['products_variations.attribute_variation_id.*'] = ['required', 'array'];
                    $this->validate['products_variations.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
                    if ($this->has('products_variations.id')) {
                        $this->validate['products_variations.id'] = ['required', 'array'];
                        $this->validate['products_variations.id.*'] = ['required', 'integer'];
                    }
                    if ($this->has('products_variations.image')) {
                        $this->validate['products_variations.image'] = ['required', 'array'];
                    }
                    if ($this->has('products_variations.price')) {
                        $this->validate['products_variations.price'] = ['required', 'array'];
                        $this->validate['products_variations.price.*'] = ['required', 'numeric'];
                    }
                    if ($this->has('products_variations.promotion_price')) {
                        $this->validate['products_variations.promotion_price'] = ['required', 'array'];
                        $this->validate['products_variations.promotion_price.*'] = ['required', 'numeric'];
                    }
                }
            }
        }
        return $this->validate;
    }

    protected function methodPut()
    {
        $this->validate = [
            'product.id' => ['required', 'exists:App\Models\Product,id'],
            'product.name' => ['required', 'string'],
            'product.desc' => ['nullable'],
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\Category,id'],
            'product.avatar' => ['required'],
            'product.price' => ['nullable', 'numeric'],
            'product.promotion_price' => ['nullable', 'numeric'],
            'product.type' => ['nullable', new Enum(ProductType::class)],
            'product.is_active' => ['required'],
            'product.is_featured' => ['required'],
            'product.is_contact_price' => ['required'],
            'product.gallery' => ['nullable'],
        ];
        if ($this->input('product.type') == ProductType::Simple->value) {
            $this->validate['product.price'] = ['required', 'numeric'];
        } elseif ($this->input('product.type') == ProductType::Variable->value) {
            // Chỉ validate product_attribute nếu có gửi lên
            if ($this->has('product_attribute')) {
                if ($this->has('product_attribute.attribute_id')) {
                    $this->validate['product_attribute.attribute_id'] = ['required', 'array'];
                    $this->validate['product_attribute.attribute_id.*'] = ['required', 'exists:App\Models\Attribute,id'];
                }
                if ($this->has('product_attribute.attribute_variation_id')) {
                    $this->validate['product_attribute.attribute_variation_id'] = ['required', 'array'];
                    $this->validate['product_attribute.attribute_variation_id.*'] = ['required', 'array'];
                    $this->validate['product_attribute.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
                }
            }
            // Chỉ validate products_variations nếu có gửi lên
            if ($this->has('products_variations')) {
                if ($this->input('products_variations.attribute_variation_id') && count($this->input('products_variations.attribute_variation_id')) > 0) {
                    $this->validate['products_variations.attribute_variation_id'] = ['required', 'array'];
                    $this->validate['products_variations.attribute_variation_id.*'] = ['required', 'array'];
                    $this->validate['products_variations.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
                    if ($this->has('products_variations.id')) {
                        $this->validate['products_variations.id'] = ['required', 'array'];
                        $this->validate['products_variations.id.*'] = ['required', 'integer'];
                    }
                    if ($this->has('products_variations.image')) {
                        $this->validate['products_variations.image'] = ['required', 'array'];
                    }
                    if ($this->has('products_variations.price')) {
                        $this->validate['products_variations.price'] = ['required', 'array'];
                        $this->validate['products_variations.price.*'] = ['required', 'numeric'];
                    }
                    if ($this->has('products_variations.promotion_price')) {
                        $this->validate['products_variations.promotion_price'] = ['required', 'array'];
                        $this->validate['products_variations.promotion_price.*'] = ['required', 'numeric'];
                    }
                }
            }
        }
        return $this->validate;
    }

    public function messages()
    {
        return [
            'product.name.required' => __('please_enter_product_name'),
            'product.name.string' => __('product.name_string'),
            'product.avatar.required' => __('please_enter_product_avatar'),
            'product.price.numeric' => __('product.price_numeric'),
            'product.price.required' => __('please_enter_price'),
            'product.price.min' => __('product.price_min'),
            'product.promotion_price.numeric' => __('product.promotion_price_numeric'),
            'product.promotion_price.required' => __('please_enter_promotion_price'),
            'product.promotion_price.min' => __('product.promotion_price_min'),
            'product.type.required' => __('please_choose_product_type'),
            'product.is_featured.required' => __('please_choose_featured_status'),
            'product.is_contact_price.required' => __('please_choose_contact_price_status'),
            'categories_id.array' => __('category.array'),
            'categories_id.*.exists' => __('category.not_exists'),
            'product_attribute.attribute_id.required' => __('please_choose_attribute'),
            'product_attribute.attribute_id.*.exists' => __('attribute.not_exists'),
            'product_attribute.attribute_variation_id.required' => __('please_choose_attribute_variation'),
            'product_attribute.attribute_variation_id.*.array' => __('attribute_variation.array'),
            'product_attribute.attribute_variation_id.*.*.exists' => __('attribute_variation.not_exists'),
            'products_variations.id.required' => __('please_enter_variation_id'),
            'products_variations.id.*.integer' => __('product.variation_id_integer'),
            'products_variations.attribute_variation_id.required' => __('please_choose_attribute_variation'),
            'products_variations.attribute_variation_id.*.array' => __('attribute_variation.array'),
            'products_variations.attribute_variation_id.*.*.exists' => __('attribute_variation.not_exists'),
            'products_variations.image.required' => __('please_enter_variation_image'),
            'products_variations.price.required' => __('please_enter_variation_price'),
            'products_variations.price.*.numeric' => __('product.variation_price_numeric'),
            'products_variations.promotion_price.required' => __('please_enter_variation_promotion_price'),
            'products_variations.promotion_price.*.numeric' => __('product.variation_promotion_price_numeric'),
        ];
    }
}
