<?php

namespace App\Api\AdminV1\Http\Requests\FlashSale;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateFlashSaleRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'start_time' => ['sometimes', 'required', 'date'],
            'end_time' => ['sometimes', 'required', 'date', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

