<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\Product;

class ChangeVariationShoppingCartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'id' => ['required'],
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'product_variation_id' => ['required', 'exists:App\Models\ProductVariation,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $product = Product::find($this->product_id);

            if ($product) {
                // Kiểm tra biến thể có thuộc về sản phẩm này không
                $isExist = $product->productVariations()->where('id', $this->product_variation_id)->first();
                if (!$isExist) {
                    $validator->errors()->add('product_variation_id', __('shopping_cart.variation_not_belong_to_product'));
                }

                // Kiểm tra sản phẩm có phải là Variable không
                if ($product->isSimple()) {
                    $validator->errors()->add('product_id', __('shopping_cart.product_has_no_variation'));
                }
            }

            // Kiểm tra item trong giỏ hàng có tồn tại không
            if (auth()->id()) {
                $cartItem = \App\Models\ShoppingCart::where('user_id', auth()->id())
                    ->where('id', $this->id)
                    ->first();
                if (!$cartItem) {
                    $validator->errors()->add('id', __('shopping_cart.item_not_in_cart'));
                } else if ($cartItem->product_id != $this->product_id) {
                    $validator->errors()->add('product_id', __('shopping_cart.product_id_mismatch'));
                }
            } else {
                $cart = session('cart', []);
                $cartItem = collect($cart)->firstWhere('id', $this->id);
                if (!$cartItem) {
                    $validator->errors()->add('id', __('shopping_cart.item_not_in_cart'));
                } else if ($cartItem['product_id'] != $this->product_id) {
                    $validator->errors()->add('product_id', __('shopping_cart.product_id_mismatch'));
                }
            }
        });
    }
}
