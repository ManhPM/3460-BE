<?php

namespace App\Api\AdminV1\Http\Requests\Order;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateOrderStatusRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'status' => ['required', 'in:pending,confirmed,processing,shipping,completed,cancelled'],
        ];
    }
}
