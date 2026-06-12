<?php

namespace App\Api\V1\Http\Requests\Order;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Models\Order;

class OrderRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'id' => ['required', 'exists:orders,id'],
            'payment_image' => ['required'],
        ];
    }

    protected function methodGet()
    {
        return [
            'status' => ['nullable'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function withValidator($validator)
    {
        if ($this->isMethod('post')) { // Chỉ áp dụng khi method là POST
            $validator->after(function ($validator) {
                $order = Order::find($this->id);
                if (!$order) {
                    $validator->errors()->add('id', __('order.not_exists'));
                }
                if (
                    $order->payment_status != PaymentStatus::Unpaid->value ||
                    $order->payment_method != PaymentMethod::Banking
                ) {
                    $validator->errors()->add('id', __('order.cannot_process'));
                }
            });
        }
    }
}
