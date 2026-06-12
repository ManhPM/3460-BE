<?php

namespace App\Api\V1\Http\Resources\VoucherProgram;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherProgramResource extends JsonResource
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
            'name' => $this->name,
            'min_order_amount' => $this->min_order_amount,
            'max_discount_value' => $this->max_discount_value,
            'discount_value' => $this->discount_value,
            'type' => $this->type,
            'voucher_type' => $this->voucher_type,
            'is_collected' => $this->is_collected,
            'qty' => $this->qty,
        ];
    }
}
