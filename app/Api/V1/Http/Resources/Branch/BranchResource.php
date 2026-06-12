<?php

namespace App\Api\V1\Http\Resources\Branch;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BranchResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($branch) {
            return [
                'id' => $branch->id,
                'branch_name' => $branch->branch_name,
                'branch_phone' => $branch->branch_phone,
                'branch_address' => $branch->branch_address,
            ];
        });
    }
}
