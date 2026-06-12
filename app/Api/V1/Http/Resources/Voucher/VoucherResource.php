<?php

namespace App\Api\V1\Http\Resources\Voucher;

use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'code' => $this->code,
            'date_end' => $this->date_end,
            'type' => $this->type->value,
            'voucher_type' => $this->voucher_type->value,
            'min_order_amount' => $this->min_order_amount,
            'max_discount_value' => $this->max_discount_value,
            'discount_value' => $this->discount_value,

        ];
    }
}
