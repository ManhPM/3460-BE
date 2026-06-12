<?php

namespace App\Api\AdminV1\Http\Resources\Voucher;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->fullname,
                    'email' => $this->user->email,
                ];
            }),
            'code' => $this->code,
            'date_end' => $this->date_end?->format('Y-m-d H:i:s'),
            'is_used' => (bool) $this->is_used,
            'min_order_amount' => $this->min_order_amount,
            'type' => $this->type?->value ?? $this->type,
            'voucher_type' => $this->voucher_type?->value ?? $this->voucher_type,
            'discount_value' => $this->discount_value,
            'max_discount_value' => $this->max_discount_value,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

