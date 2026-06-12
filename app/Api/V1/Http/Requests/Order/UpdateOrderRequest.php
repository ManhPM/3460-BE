<?php

namespace App\Api\V1\Http\Requests\Order;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\Order;

class UpdateOrderRequest extends BaseRequest
{
    protected function methodPut()
    {
        return [
            'bank_id' => ['nullable', 'integer', 'exists:banks,id'],
            'payment_image' => ['nullable', 'string'],
        ];
    }

    protected function withValidator($validator)
    {
        if ($this->isMethod('put')) {
            $validator->after(function ($validator) {
                $orderId = $this->route('id');
                $order = Order::find($orderId);

                if (!$order) {
                    $validator->errors()->add('id', __('order.not_exists'));
                    return;
                }

                // Check if order belongs to current user
                $user = auth('user')->user();
                if ($order->user_id != $user->id) {
                    $validator->errors()->add('id', __('order.no_permission_to_update'));
                }
            });
        }
    }
}

