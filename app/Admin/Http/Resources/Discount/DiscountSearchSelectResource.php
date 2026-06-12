<?php

namespace App\Admin\Http\Resources\Discount;

use App\Enums\Discount\DiscountValueType;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountSearchSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->type == DiscountValueType::Money) {
            $type = format_price($this->discount_value);
        } else {
            $type = $this->discount_value . '%';
        }
        return [
            'id' => $this->id,
            'text' => $this->code . ' | Tối thiểu: ' . format_price($this->min_order_amount) . ' | Còn lại: ' . $this->max_usage . ' | Giảm: ' . $type
        ];
    }
}
