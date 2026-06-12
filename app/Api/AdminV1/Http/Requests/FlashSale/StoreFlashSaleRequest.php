<?php

namespace App\Api\AdminV1\Http\Requests\FlashSale;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreFlashSaleRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

