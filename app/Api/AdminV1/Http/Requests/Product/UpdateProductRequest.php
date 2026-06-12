<?php

namespace App\Api\AdminV1\Http\Requests\Product;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateProductRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        $productId = $this->route('product');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:products,slug,' . $productId],
            'sku' => ['sometimes', 'required', 'string', 'unique:products,sku,' . $productId],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:active,inactive,draft'],
            'is_featured' => ['boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
        ];
    }
}
