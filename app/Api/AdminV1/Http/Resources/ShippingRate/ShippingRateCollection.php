<?php

namespace App\Api\AdminV1\Http\Resources\ShippingRate;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShippingRateCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => ShippingRateResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}

