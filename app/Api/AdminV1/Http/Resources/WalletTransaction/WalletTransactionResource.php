<?php

namespace App\Api\AdminV1\Http\Resources\WalletTransaction;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
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
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'note' => $this->note,
            'order_id' => $this->order_id,
            'proof_image' => $this->proof_image ? asset($this->proof_image) : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
