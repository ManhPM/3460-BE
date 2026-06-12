<?php

namespace App\Api\AdminV1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    protected $pagination;

    public function __construct($resource)
    {
        $this->pagination = $resource;
        parent::__construct($resource->items());
    }

    public function toArray($request): array
    {
        return [
            'data' => OrderBasicResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->pagination->currentPage(),
                'last_page' => $this->pagination->lastPage(),
                'per_page' => $this->pagination->perPage(),
                'total' => $this->pagination->total(),
            ],
            'links' => [
                'first' => $this->pagination->url(1),
                'last' => $this->pagination->url($this->pagination->lastPage()),
                'prev' => $this->pagination->previousPageUrl(),
                'next' => $this->pagination->nextPageUrl(),
            ],
        ];
    }
}

