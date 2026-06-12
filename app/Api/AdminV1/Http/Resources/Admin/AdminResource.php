<?php

namespace App\Api\AdminV1\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? $this->fullname,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'status' => $this->status,
            'branch_name' => $this->branch_name,
            'branch_phone' => $this->branch_phone,
            'branch_address' => $this->branch_address,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'title' => $role->title ?? $role->name,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
