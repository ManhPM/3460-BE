<?php

namespace App\Api\AdminV1\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'position' => $this->position,
            'description' => $this->description,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'is_home' => $this->is_home,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
