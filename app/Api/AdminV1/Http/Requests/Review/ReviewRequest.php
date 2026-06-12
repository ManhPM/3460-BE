<?php

namespace App\Api\AdminV1\Http\Requests\Review;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
                    $validator->errors()->add('product_id', __('review_exists'));
                }
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => __('please_choose_user'),
            'user_id.exists' => __('user_not_found'),

            'product_id.required' => __('please_choose_product'),
            'product_id.exists' => __('product_id_not_exists'),

            'rating.required' => __('please_enter_rating'),
            'rating.numeric' => __('review_rating_numeric'),
            'rating.min' => __('review_rating_min'),
            'rating.max' => __('review_rating_max'),

            'id.required' => __('please_enter_review_id'),
            'id.exists' => __('review.not_exists'),
        ];
    }
}

