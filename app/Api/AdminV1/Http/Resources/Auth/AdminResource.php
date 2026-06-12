<?php

namespace App\Api\AdminV1\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->fullname ?? $this->name ?? '',
            'email' => $this->email,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'role' => $this->role?->name,
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
