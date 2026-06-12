<?php

namespace App\Api\V1\Http\Requests\Ward;

use App\Api\V1\Http\Requests\BaseRequest;

class WardSearchRequest extends BaseRequest
{
    protected function methodGet()
    {
        return [
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'name' => ['nullable']
        ];
    }
}
