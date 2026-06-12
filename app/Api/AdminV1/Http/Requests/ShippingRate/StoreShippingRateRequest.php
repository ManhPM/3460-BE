<?php

namespace App\Api\AdminV1\Http\Requests\ShippingRate;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreShippingRateRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'price' => ['required', 'numeric', 'min:0'],
            'province_id' => ['required', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
        ];
    }
}

