<?php

namespace App\Api\AdminV1\Http\Requests\Slider;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexSliderRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

