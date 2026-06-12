<?php

namespace App\Admin\Http\Requests\Review;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Order\OrderReview;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Validation\Validator;

class ReviewRequest extends BaseRequest
{

    protected function methodPost(): array
    {
        return [
            'user_id' => ['required', 'exists:App\Models\User,id'],
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'content' => ['nullable'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Review,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'content' => ['nullable'],
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('post')) {
                $isExist = Review::where('product_id', $this->product_id)->where('user_id', $this->user_id)->first();
                if ($isExist) {
                    $validator->errors()->add('product_id', __('Đánh giá đã tồn tại.'));
                }
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => 'Người dùng là bắt buộc.',
            'user_id.exists' => 'Người dùng không hợp lệ.',

            'product_id.required' => 'Sản phẩm là bắt buộc.',
            'product_id.exists' => 'Sản phẩm không hợp lệ.',

            'rating.required' => 'Xếp hạng là bắt buộc.',
            'rating.numeric' => 'Xếp hạng phải là một số.',
            'rating.min' => 'Xếp hạng tối thiểu là 1.',
            'rating.max' => 'Xếp hạng tối đa là 5.',

            'id.required' => 'ID đánh giá là bắt buộc.',
            'id.exists' => 'Đánh giá không hợp lệ.',
        ];
    }
}
