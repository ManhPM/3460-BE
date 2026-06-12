<?php

namespace App\Api\V1\Http\Requests\Order;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Contact\ContactReason;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use Illuminate\Validation\Rules\Enum;

class CancelOrderRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'cancel_reason' => ['required'],
            'id' => ['required', 'exists:orders,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $orderId = $this->input('id');

            $user = auth()->user();
            $order = Order::find($orderId);

            if (!$user || !$order) {
                return;
            }

            if ($order && $order->status == OrderStatus::Cancelled) {
                $validator->errors()->add('id', __('order.already_cancelled'));
            }
        });
    }
}
