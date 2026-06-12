<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreProductRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:products,slug'],
            'sku' => ['required', 'string', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,draft'],
            'is_featured' => ['boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('please_enter_product_name'),
            'sku.required' => __('please_enter_sku'),
            'sku.unique' => __('product.sku_unique'),
            'price.required' => __('please_enter_price'),
            'category_id.required' => __('category.required'),
            'category_id.exists' => __('category.not_exists'),
        ];
    }
}
