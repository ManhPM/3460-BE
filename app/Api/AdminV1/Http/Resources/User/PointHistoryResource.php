<?php

namespace App\Api\AdminV1\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class PointHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'points_earned' => $this->points_earned ?? 0,
            'points_used' => $this->points ?? 0,
            'total' => $this->total ?? 0,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
