<?php

namespace App\Api\AdminV1\Http\Requests\Slider;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreSliderRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ];
    }
}

