<?php

namespace App\Admin\Http\Requests\Product;

use App\Admin\Http\Requests\BaseRequest;
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
            'product_attribute.attribute_id.required' => 'Vui lòng chọn thuộc tính.',
            'product_attribute.attribute_id.array' => 'Vui lòng chọn thuộc tính.',
            'product_attribute.attribute_id.*.required' => 'Vui lòng chọn thuộc tính.',
            'product_attribute.attribute_id.*.exists' => 'Thuộc tính không hợp lệ.',

            'product_attribute.attribute_variation_id.required' => 'Vui lòng chọn biến thể thuộc tính.',
            'product_attribute.attribute_variation_id.array' => 'Vui lòng chọn biến thể thuộc tính.',
            'product_attribute.attribute_variation_id.*.required' => 'Vui lòng chọn biến thể thuộc tính.',
            'product_attribute.attribute_variation_id.*.*.required' => 'Vui lòng chọn biến thể thuộc tính.',
            'product_attribute.attribute_variation_id.*.*.exists' => 'Biến thể thuộc tính không hợp lệ.',

            'variation_action.required' => 'Hành động biến thể là bắt buộc.',
            'variation_action.enum_value' => 'Hành động biến thể không hợp lệ.',

            'name.required' => 'Tên biến thể là bắt buộc.',
            'name.string' => 'Tên biến thể phải là một chuỗi.',

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
