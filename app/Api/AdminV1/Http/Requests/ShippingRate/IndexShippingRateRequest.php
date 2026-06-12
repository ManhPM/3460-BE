<?php

namespace App\Api\AdminV1\Http\Requests\ShippingRate;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexShippingRateRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'province_id' => ['nullable', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

