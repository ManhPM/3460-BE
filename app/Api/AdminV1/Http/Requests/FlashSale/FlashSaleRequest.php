<?php

namespace App\Api\AdminV1\Http\Requests\FlashSale;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Models\FlashSaleDetail;

class FlashSaleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'unique:App\Models\FlashSale,name'],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',

            'product_id' => ['required', 'array'],
            'product_id.*' => ['required', 'exists:App\Models\Product,id'],
            'product_variation_id' => ['nullable', 'array'],
            'product_variation_id.*' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            'product_variation_flashsale_price' => ['nullable', 'array'],
            'product_variation_flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
            'product_variation_qty' => ['nullable', 'array'],
            'product_variation_qty.*' => ['nullable', 'integer', 'min:1'],
            'qty' => ['nullable', 'array'],
            'qty.*' => ['nullable', 'integer', 'min:1'],
            'flashsale_price' => ['nullable', 'array'],
            'flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\FlashSale,id'],
            'name' => ['required', 'unique:App\Models\FlashSale,name,' . $this->id],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',

            'product_id' => ['required', 'array'],
            'product_id.*' => ['required', 'exists:App\Models\Product,id'],
            'product_variation_id' => ['nullable', 'array'],
            'product_variation_id.*' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            'product_variation_flashsale_price' => ['nullable', 'array'],
            'product_variation_flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
            'product_variation_qty' => ['nullable', 'array'],
            'product_variation_qty.*' => ['nullable', 'integer', 'min:1'],
            'qty' => ['nullable', 'array'],
            'qty.*' => ['nullable', 'integer', 'min:1'],
            'flashsale_price' => ['nullable', 'array'],
            'flashsale_price.*' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required'],
        ];
    }

    protected function methodGet()
    {
        if ($this->routeIs('admin.flashsale.add_product')) {
            return [
                'product_slug' => ['required', 'exists:App\Models\Product,slug'],
                'product_variation_id' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'name.required' => __('please_enter_flash_sale_name'),
            'name.unique' => __('flash_sale.name_unique'),
            'start_time.required' => __('please_enter_start_time'),
            'start_time.date' => __('flash_sale.start_time_invalid'),
            'end_time.required' => __('please_enter_end_time'),
            'end_time.date' => __('flash_sale.end_time_invalid'),
            'end_time.after_or_equal' => __('flash_sale.end_time_after_start'),
            'product_id.required' => __('please_choose_at_least_one_product'),
            'product_id.array' => __('flash_sale.product_list_invalid'),
            'product_id.*.required' => __('flash_sale.product_required'),
            'product_id.*.exists' => __('product_id_not_exists'),
            'qty.required' => __('please_enter_product_quantity'),
            'qty.array' => __('flash_sale.qty_list_invalid'),
            'flashsale_price.required' => __('please_enter_flash_sale_price'),
            'flashsale_price.array' => __('flash_sale.price_list_invalid'),
            'product_variation_flashsale_price.array' => __('flash_sale.variation_price_list_invalid'),
            'product_variation_qty.array' => __('flash_sale.variation_qty_list_invalid'),
            'product_variation_qty.*.integer' => __('quantity_must_be_integer'),
            'product_variation_qty.*.min' => __('quantity_min_value'),
            'qty.*.integer' => __('quantity_must_be_integer'),
            'qty.*.min' => __('quantity_min_value'),
            'flashsale_price.*.numeric' => __('flash_sale.price_numeric'),
            'flashsale_price.*.min' => __('flash_sale.price_min'),
            'product_variation_flashsale_price.*.numeric' => __('flash_sale.variation_price_numeric'),
            'product_variation_flashsale_price.*.min' => __('flash_sale.variation_price_min'),
            'id.required' => __('please_enter_flash_sale_id'),
            'id.exists' => __('flash_sale.not_exists'),
            'product_slug.required' => __('please_enter_product_slug'),
            'product_slug.exists' => __('product_slug_not_exists'),
            'product_variation_id.exists' => __('product_variation_id_not_exists'),
        ];
    }
}
