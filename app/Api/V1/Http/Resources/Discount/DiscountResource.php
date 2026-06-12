<?php

namespace App\Api\V1\Http\Resources\Discount;

use App\Enums\Discount\DiscountValueType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'max_usage' => $this->max_usage,
            'min_order_amount' => $this->min_order_amount,
            'max_discount_value' => $this->max_discount_value,
            'max_usage_per_user' => $this->max_usage_per_user,
            'discount_value' => $this->discount_value,
            'type' => $this->type->value,
        ];
    }
}
