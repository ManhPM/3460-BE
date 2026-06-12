<?php

namespace App\Api\AdminV1\Http\Resources\ShippingRate;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'province_id' => $this->province_id,
            'province' => $this->whenLoaded('province', function () {
                return [
                    'id' => $this->province->id,
                    'name' => $this->province->name,
                ];
            }),
            'ward_id' => $this->ward_id,
            'ward' => $this->whenLoaded('ward', function () {
                return [
                    'id' => $this->ward->id,
                    'name' => $this->ward->name,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

