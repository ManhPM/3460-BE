<?php

namespace App\Api\V1\Http\Requests\Product;

use App\Api\V1\Http\Requests\BaseRequest;

class ProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'limit' => ['nullable', 'string'],
            'page' => ['nullable', 'string'],
            'min_product_price' => ['nullable', 'string'],
            'max_product_price' => ['nullable', 'string'],
            'category_ids' => ['nullable', 'array'],
            'color_slugs' => ['nullable', 'array'],
            'size_slugs' => ['nullable', 'array'],
            'sort' => ['nullable', 'string'],
        ];
    }
}
