<?php

namespace App\Api\AdminV1\Http\Resources\CommissionWithdrawal;

use Illuminate\Http\Resources\Json\JsonResource;

class CommissionWithdrawalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'amount' => $this->amount,
            'status' => $this->status,
            'note' => $this->note,
            'bank_info' => $this->bank_info,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

