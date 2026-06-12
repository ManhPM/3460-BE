<?php

namespace App\Api\V1\Http\Requests\ShoppingCart;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\Product;

class CreateShoppingCartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        $product = Product::findOrFail($this->product_id);
        if ($product && $product->isSimple()) {
            return [
                'product_id' => ['required', 'exists:App\Models\Product,id'],
                'qty' => ['required', 'integer', 'min:1'],
                'admin_id' => ['required', 'exists:App\Models\Admin,id'],
            ];
        } else {
            return [
                'product_id' => ['required', 'exists:App\Models\Product,id'],
                'variation_id' => ['required'],
                'qty' => ['required', 'integer', 'min:1'],
                'admin_id' => ['required', 'exists:App\Models\Admin,id'],
            ];
        }
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $product = Product::find($this->product_id);

            if ($product && !$product->isSimple()) {
                $variation_id = $this->variation_id;
                $isExist = $product->productVariations()->where('id', $variation_id)->first();
                if (!$isExist) {
                    $validator->errors()->add('variation_id', __('product_variation_id_not_exists'));
                }
            }
        });
    }
}
