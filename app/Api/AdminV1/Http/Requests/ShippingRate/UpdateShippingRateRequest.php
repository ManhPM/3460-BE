<?php

namespace App\Api\AdminV1\Http\Requests\ShippingRate;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateShippingRateRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'province_id' => ['sometimes', 'required', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
        ];
    }
}

