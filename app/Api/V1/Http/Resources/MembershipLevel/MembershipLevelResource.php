<?php

namespace App\Api\V1\Http\Resources\MembershipLevel;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipLevelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'min_points' => $this->min_points ?? 0,
            'discount_percentage' => $this->discount_percentage ?? 0,
            'color_1' => $this->color_1 ?? '',
            'color_2' => $this->color_2 ?? '',
            'color_3' => $this->color_3 ?? '',
            'icon' => $this->icon ? asset($this->icon) : null,
            'description' => $this->description ?? '',
        ];
    }
}

