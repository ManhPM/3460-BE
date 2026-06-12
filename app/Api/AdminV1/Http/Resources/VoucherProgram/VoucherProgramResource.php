<?php

namespace App\Api\AdminV1\Http\Resources\VoucherProgram;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherProgramResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'expiration_days' => $this->expiration_days,
            'min_order_amount' => $this->min_order_amount,
            'max_discount_value' => $this->max_discount_value,
            'discount_value' => $this->discount_value,
            'type' => $this->type?->value,
            'voucher_type' => $this->voucher_type?->value,
            'avatar' => $this->avatar,
            'qty' => $this->qty,
            'status' => $this->status ?? 1,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

