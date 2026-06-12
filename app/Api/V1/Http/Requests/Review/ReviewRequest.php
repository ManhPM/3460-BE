<?php

namespace App\Api\V1\Http\Requests\Review;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Validation\Validator;

class ReviewRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'order_detail_id' => ['required', 'exists:App\Models\OrderDetail,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'content' => ['nullable'],
            'images' => ['nullable', 'array'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'order_id' => ['required', 'exists:App\Models\Order,id']
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('POST')) {
                $orderDetail = \App\Models\OrderDetail::find($this->order_detail_id);
                if (!$orderDetail) {
                    $validator->errors()->add('order_detail_id', __('order_detail.not_exists'));
                    return;
                }
                $order = $orderDetail->order;
                if ($order->status != OrderStatus::Completed) {
                    $validator->errors()->add('order_detail_id', __('review.order_not_completed'));
                }
                if ($orderDetail->is_reviewed) {
                    $validator->errors()->add('order_detail_id', __('review.already_reviewed'));
                }
            }
            if ($this->isMethod('GET')) {
                $order = Order::find($this->order_id);
                if ($order && ($order->status != OrderStatus::Completed)) {
                    $validator->errors()->add('order_id', __('review.order_not_completed'));
                }
            }
        });
    }
}
