<?php

namespace App\Api\AdminV1\Http\Resources\Bank;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'logo' => $this->logo ? asset($this->logo) : null,
            'is_active' => $this->is_active,
            'bank_account' => $this->bank_account,
            'bank_account_number' => $this->bank_account_number,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
