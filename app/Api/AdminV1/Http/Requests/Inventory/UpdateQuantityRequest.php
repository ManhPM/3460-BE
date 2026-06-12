<?php

namespace App\Api\AdminV1\Http\Requests\Inventory;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateQuantityRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'admin_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer', 'required_without:product_variation_id'],
            'product_variation_id' => ['nullable', 'integer', 'required_without:product_id'],
            'qty' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'qty.required' => __('please_enter_quantity'),
            'qty.integer' => __('quantity_must_be_integer'),
            'qty.min' => __('quantity_min_value'),
        ];
    }
}
