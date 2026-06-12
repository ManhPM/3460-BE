<?php

namespace App\Api\AdminV1\Http\Requests\ShippingRate;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class ShippingRateRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'price' => ['required', 'numeric', 'min:1'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\ShippingRate,id'],
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'price' => ['required', 'numeric', 'min:1'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {});
    }

    public function messages()
    {
        return [
            'province_id.required' => __('please_choose_province'),
            'province_id.exists' => __('province_not_exists'),

            'ward_id.exists' => __('ward_not_exists'),

            'price.required' => __('please_enter_shipping_price'),
            'price.numeric' => __('shipping_rate.price_numeric'),
            'price.min' => __('shipping_rate.price_min'),

            'id.required' => __('please_enter_id'),
            'id.exists' => __('shipping_rate.id_not_exists'),
        ];
    }
}
