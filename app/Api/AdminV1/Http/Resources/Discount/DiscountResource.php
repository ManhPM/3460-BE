<?php

namespace App\Api\AdminV1\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'date_start' => $this->date_start?->format('Y-m-d H:i:s'),
            'date_end' => $this->date_end?->format('Y-m-d H:i:s'),
            'max_usage' => $this->max_usage,
            'min_order_amount' => $this->min_order_amount,
            'type' => $this->type?->value ?? $this->type,
            'discount_value' => $this->discount_value,
            'max_discount_value' => $this->max_discount_value,
            'max_usage_per_user' => $this->max_usage_per_user,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
