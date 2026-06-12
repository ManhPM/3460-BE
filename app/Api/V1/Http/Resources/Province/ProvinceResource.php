<?php

namespace App\Api\V1\Http\Resources\Province;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProvinceResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        });
    }
}
