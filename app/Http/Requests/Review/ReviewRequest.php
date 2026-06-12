<?php

namespace App\Http\Requests\Review;

use App\Admin\Http\Requests\BaseRequest;

class ReviewRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'user_id' => ['required', 'exists:App\Models\User,id'],
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'content' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Review,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'content' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'ID người dùng là bắt buộc.',
            'user_id.exists' => 'Người dùng không tồn tại.',
            'product_id.required' => 'ID sản phẩm là bắt buộc.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'rating.required' => 'Đánh giá là bắt buộc.',
            'rating.numeric' => 'Đánh giá phải là một số.',
            'rating.min' => 'Đánh giá tối thiểu là 1.',
            'rating.max' => 'Đánh giá tối đa là 5.',
            'content.required' => 'Nội dung đánh giá là bắt buộc.',
            'id.required' => 'ID đánh giá là bắt buộc.',
            'id.exists' => 'Đánh giá không tồn tại.',
        ];
    }
}
