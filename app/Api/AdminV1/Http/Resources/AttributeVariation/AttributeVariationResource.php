<?php

namespace App\Api\AdminV1\Http\Resources\AttributeVariation;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeVariationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'name' => $this->name,
            'position' => $this->position,
            'meta_value' => $this->meta_value,
            'desc' => $this->desc,
            'attribute' => $this->whenLoaded('attribute', function () {
                return [
                    'id' => $this->attribute->id,
                    'name' => $this->attribute->name,
                    'type' => $this->attribute->type?->value ?? $this->attribute->type,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
