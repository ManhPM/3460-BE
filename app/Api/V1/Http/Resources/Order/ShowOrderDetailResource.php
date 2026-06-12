<?php

namespace App\Api\V1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Product\ProductType;
use Illuminate\Support\Facades\Log;

class ShowOrderDetailResource extends JsonResource
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
        $data = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->product_name,
            'qty' => $this->qty,
            'unit_price' => $this->unit_price,
            'avatar' => asset($this->product_avatar),
            'is_reviewed' => $this->is_reviewed
        ];
        if ($this->product->type == ProductType::Variable) {
            $data['variation_id'] = $this->product_variation_id;
            $data['attribute_variations']  = collect($this->productVariation->attribute_variations)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            });
        }
        return $data;
    }
}
