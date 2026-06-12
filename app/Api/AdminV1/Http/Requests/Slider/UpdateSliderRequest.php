<?php

namespace App\Api\AdminV1\Http\Requests\Slider;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateSliderRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ];
    }
}

