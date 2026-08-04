<?php

namespace App\Admin\Http\Requests\Product;

use App\Admin\Http\Requests\BaseRequest;
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
            $this->validate['product_attribute.attribute_id'] = ['nullable', 'array'];
            $this->validate['product_attribute.attribute_id.*'] = ['required', 'exists:App\Models\Attribute,id'];
            $this->validate['product_attribute.attribute_variation_id'] = ['nullable', 'array'];
            $this->validate['product_attribute.attribute_variation_id.*'] = ['nullable', 'array'];
            $this->validate['product_attribute.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
            $this->validate['products_variations.attribute_variation_id'] = ['nullable', 'array'];
            if ($this->input('products_variations.attribute_variation_id') && count($this->input('products_variations.attribute_variation_id')) > 0) {
                $this->validate['products_variations.id'] = ['nullable', 'array'];
                $this->validate['products_variations.id.*'] = ['required', 'integer'];
                $this->validate['products_variations.attribute_variation_id'] = ['nullable', 'array'];
                $this->validate['products_variations.attribute_variation_id.*'] = ['nullable', 'array'];
                $this->validate['products_variations.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
                $this->validate['products_variations.image'] = ['nullable', 'array'];
                $this->validate['products_variations.price'] = ['nullable', 'array'];
                $this->validate['products_variations.price.*'] = ['required', 'numeric'];
                $this->validate['products_variations.promotion_price'] = ['nullable', 'array'];
                $this->validate['products_variations.promotion_price.*'] = ['nullable', 'numeric'];
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
            $this->validate['product_attribute.attribute_id'] = ['nullable', 'array'];
            $this->validate['product_attribute.attribute_id.*'] = ['required', 'exists:App\Models\Attribute,id'];
            $this->validate['product_attribute.attribute_variation_id'] = ['nullable', 'array'];
            $this->validate['product_attribute.attribute_variation_id.*'] = ['nullable', 'array'];
            $this->validate['product_attribute.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
            $this->validate['products_variations.attribute_variation_id'] = ['nullable', 'array'];
            if ($this->input('products_variations.attribute_variation_id') && count($this->input('products_variations.attribute_variation_id')) > 0) {
                $this->validate['products_variations.id'] = ['nullable', 'array'];
                $this->validate['products_variations.id.*'] = ['required', 'integer'];
                $this->validate['products_variations.attribute_variation_id'] = ['nullable', 'array'];
                $this->validate['products_variations.attribute_variation_id.*'] = ['nullable', 'array'];
                $this->validate['products_variations.attribute_variation_id.*.*'] = ['required', 'exists:App\Models\AttributeVariation,id'];
                $this->validate['products_variations.image'] = ['nullable', 'array'];
                $this->validate['products_variations.price'] = ['nullable', 'array'];
                $this->validate['products_variations.price.*'] = ['required', 'numeric'];
                $this->validate['products_variations.promotion_price'] = ['nullable', 'array'];
                $this->validate['products_variations.promotion_price.*'] = ['nullable', 'numeric'];
            }
        }
        return $this->validate;
    }

    public function messages()
    {
        return [
            'product.name.required' => 'Tên sản phẩm là bắt buộc.',
            'product.name.string' => 'Tên sản phẩm phải là chuỗi.',
            'product.avatar.required' => 'Ảnh đại diện sản phẩm là bắt buộc.',
            'product.price.numeric' => 'Giá sản phẩm phải là số.',
            'product.price.required' => 'Giá sản phẩm là bắt buộc.',
            'product.price.min' => 'Giá sản phẩm phải lớn hơn 0.',
            'product.promotion_price.numeric' => 'Giá khuyến mãi phải là số.',
            'product.promotion_price.required' => 'Giá khuyến mãi là bắt buộc.',
            'product.promotion_price.min' => 'Giá khuyến mãi phải lớn hơn 0.',
            'product.type.required' => 'Loại sản phẩm là bắt buộc.',
            'product.is_featured.required' => 'Trạng thái nổi bật là bắt buộc.',
            'product.is_contact_price.required' => 'Trạng thái liên hệ giá là bắt buộc.',
            'categories_id.array' => 'Danh mục phải là mảng.',
            'categories_id.*.exists' => 'Danh mục không tồn tại.',
            'product_attribute.attribute_id.required' => 'Thuộc tính sản phẩm là bắt buộc.',
            'product_attribute.attribute_id.*.exists' => 'Thuộc tính sản phẩm không tồn tại.',
            'product_attribute.attribute_variation_id.required' => 'Biến thể thuộc tính sản phẩm là bắt buộc.',
            'product_attribute.attribute_variation_id.*.array' => 'Biến thể thuộc tính sản phẩm phải là mảng.',
            'product_attribute.attribute_variation_id.*.*.exists' => 'Biến thể thuộc tính sản phẩm không tồn tại.',
            'products_variations.id.required' => 'ID biến thể sản phẩm là bắt buộc.',
            'products_variations.id.*.integer' => 'ID biến thể sản phẩm phải là số nguyên.',
            'products_variations.attribute_variation_id.required' => 'Biến thể thuộc tính sản phẩm là bắt buộc.',
            'products_variations.attribute_variation_id.*.array' => 'Biến thể thuộc tính sản phẩm phải là mảng.',
            'products_variations.attribute_variation_id.*.*.exists' => 'Biến thể thuộc tính sản phẩm không tồn tại.',
            'products_variations.image.required' => 'Ảnh biến thể sản phẩm là bắt buộc.',
            'products_variations.price.required' => 'Giá biến thể sản phẩm là bắt buộc.',
            'products_variations.price.*.numeric' => 'Giá biến thể sản phẩm phải là số.',
            'products_variations.promotion_price.required' => 'Giá khuyến mãi biến thể sản phẩm là bắt buộc.',
            'products_variations.promotion_price.*.numeric' => 'Giá khuyến mãi biến thể sản phẩm phải là số.',
        ];
    }
}
