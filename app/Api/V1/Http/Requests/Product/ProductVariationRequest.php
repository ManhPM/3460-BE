<?php

namespace App\Api\V1\Http\Requests\Product;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\Product\ProductHasVariation;

class ProductVariationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'variation_id' => ['required', 'array', new ProductHasVariation($this->product_id)],
            'variation_id.*' => ['required', 'exists:App\Models\AttributeVariation,id'],
        ];
    }
}
