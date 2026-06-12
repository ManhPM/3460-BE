<?php

namespace App\Admin\Http\Resources\Icon;

use Illuminate\Http\Resources\Json\JsonResource;

class IconSearchSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->name,
            'text' => "<i class=\"{$this->name}\">before</i> {$this->name}"
        ];
    }
}
