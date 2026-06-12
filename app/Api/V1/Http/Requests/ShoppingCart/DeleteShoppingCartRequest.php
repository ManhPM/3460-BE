<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Api\V1\Http\Requests\BaseRequest;

class DeleteShoppingCartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'id' => ['required', 'array'],
            'id.*' => ['required'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $inputIds = request()->input('id', []);

            if (auth()->id()) {
                // Trường hợp người dùng đã đăng nhập
                $userId = auth()->id();
                $invalidIds = \App\Models\ShoppingCart::whereIn('id', $inputIds)
                    ->where('user_id', '!=', $userId)
                    ->pluck('id')
                    ->toArray();

                if (!empty($invalidIds)) {
                    $validator->errors()->add('id', __('shopping_cart.invalid_cart_items'));
                }
            } else {
                // Trường hợp người dùng chưa đăng nhập
                $cart = session('cart', []);
                $cartIds = array_column($cart, 'id'); // Lấy tất cả 'id' từ cart trong cookie

                $missingIds = array_diff($inputIds, $cartIds);
                if (!empty($missingIds)) {
                    $validator->errors()->add('id', __('shopping_cart.invalid_cart_items'));
                }
            }
        });
    }
}
