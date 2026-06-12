<?php

namespace App\Api\V1\Http\Requests\Review;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Validation\Validator;

class UpdateReviewRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'id' => ['required', 'exists:App\Models\Review,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'order_id' => ['required', 'exists:App\Models\Order,id'],
            'content' => ['nullable']
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $review = Review::find($this->id);

            $order = Order::find($this->order_id);
            if ($order->status != Completed->value) {
                $validator->errors()->add('order_id', __('review.order_not_completed'));
            }
        });
    }
}
