<?php

namespace App\Api\V1\Http\Requests\Product;

use App\Api\V1\Http\Requests\BaseRequest;

class ProductSearchRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'keyword' => 'sometimes|string|max:255',
            'page' =>'required|integer',
            'limit' => 'sometimes|required|integer|min:1',
        ];
    }
    protected function methodPost(): array
    {
        return [
            'keyword' => 'sometimes|string|max:255',
            'page' => 'required|integer|min:1',
            'limit' => 'sometimes|required|integer|min:1|max:100',
        ];
    }

}
