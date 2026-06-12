<?php

namespace App\Api\AdminV1\Http\Requests\FlashSale;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexFlashSaleRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
