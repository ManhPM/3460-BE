<?php

namespace App\Api\V1\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AllMembershipLevelResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return new MembershipLevelResource($item);
        });
    }
}
