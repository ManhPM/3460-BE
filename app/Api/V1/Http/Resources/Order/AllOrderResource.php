<?php

namespace App\Api\V1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AllOrderResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($order) {
            return new ShowOrderResource($order);
        });
    }
}
