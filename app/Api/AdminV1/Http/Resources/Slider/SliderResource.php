<?php

namespace App\Api\AdminV1\Http\Resources\Slider;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'plain_key' => $this->plain_key,
            'desc' => $this->desc,
            'status' => $this->status?->value ?? $this->status,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'slider_id' => $item->slider_id,
                        'title' => $item->title,
                        'image' => $item->image ? asset($item->image) : null,
                        'avatar' => $item->avatar ? asset($item->avatar) : null,
                        'mobile_avatar' => $item->mobile_avatar ? asset($item->mobile_avatar) : null,
                        'link' => $item->link,
                        'position' => $item->position,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
