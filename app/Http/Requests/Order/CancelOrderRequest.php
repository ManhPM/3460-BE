<?php

namespace App\Http\Requests\Order;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Models\Order;

class CancelOrderRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'id' => ['required', 'exists:orders,id'],
            'cancel_reason' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $orderId = $this->input('id');

            $user = auth('web')->user();
            $order = Order::find($orderId);

            if (!$user || !$order) {
                return;
            }

            if ($order && $order->status == OrderStatus::Cancelled) {
                $validator->errors()->add('id', 'Đơn hàng đã bị hủy rồi không thể huỷ tiếp.');
            }
        });
    }
}
