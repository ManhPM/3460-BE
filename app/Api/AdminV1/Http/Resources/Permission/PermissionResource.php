<?php

namespace App\Api\AdminV1\Http\Resources\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title ?? $this->name,
            'guard_name' => $this->guard_name,
            'module' => $this->whenLoaded('module', function () {
                return $this->module ? [
                    'id' => $this->module->id,
                    'name' => $this->module->name,
                ] : null;
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
