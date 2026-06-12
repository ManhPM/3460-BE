<?php

namespace App\Api\V1\Http\Resources\Ward;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WardResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'province_id' => $item->province_id,
            ];
        });
    }
}
