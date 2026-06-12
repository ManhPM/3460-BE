<?php

namespace App\Api\AdminV1\Http\Requests\Order;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateOrderRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'status' => ['sometimes', 'in:pending,confirmed,processing,shipping,completed,cancelled'],
            'payment_status' => ['sometimes', 'in:pending,paid,failed'],
            'note' => ['nullable', 'string'],
        ];
    }
}
