<?php

namespace App\Api\V1\Http\Resources\Wishlist;

use App\Api\V1\Http\Resources\Product\ShowProductResource;
use App\Enums\Product\ProductType;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Support\AuthSupport;

class AllWishlistResource extends ResourceCollection
{
    use AuthSupport;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  $this->collection->map(function ($item) {
            $data = new ShowProductResource($item->product);
            return $data;
        });
    }
}
