<?php

namespace App\Api\AdminV1\Http\Resources\MembershipLevel;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipLevelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'min_points' => $this->min_points,
            'discount_percentage' => $this->discount_percentage,
            'color_1' => $this->color_1,
            'color_2' => $this->color_2,
            'color_3' => $this->color_3,
            'icon' => $this->icon,
            'description' => $this->description,
            'users_count' => $this->whenLoaded('users', function () {
                return $this->users->count();
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

