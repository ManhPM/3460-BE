<?php

namespace App\Api\V1\Rules\Product;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ProductAttribute;

class ProductHasVariation implements Rule
{
    private $product_id;
    private $countAttribute;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($product_id)
    {
        //
        $this->product_id = $product_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        $this->countAttribute = ProductAttribute::where('product_id', $this->product_id)->count();
        if(gettype($value) === 'array' && $this->countAttribute > 0 && $this->countAttribute == count($value)){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->countAttribute == 0){
            return __('product.has_no_variation');
        }
        return __('product.has_attributes_need_variations', ['attribute' => $this->countAttribute]);
    }
}
