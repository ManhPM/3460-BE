<?php

namespace App\Api\V1\Http\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Nếu là collection, chuyển thành array key-value
        if ($this->resource instanceof \Illuminate\Support\Collection) {
            $result = [];
            foreach ($this->resource as $setting) {
                $result[$setting->setting_key] = $setting->plain_value;
            }
            return $result;
        }

        // Nếu là single resource
        return [
            $this->setting_key => $this->plain_value
        ];
    }
}
