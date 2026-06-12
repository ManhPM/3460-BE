<?php

namespace App\Api\AdminV1\Http\Resources\PostCategory;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCategoryCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => PostCategoryResource::collection($this->collection),
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

