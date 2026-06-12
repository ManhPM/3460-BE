<?php

namespace App\Api\V1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AllOrderDetailResource extends ResourceCollection
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
            return [
                'order_code' => $item->order->code,
                'product_name' => $item->product_name,
                'total' => $item->unit_price * $item->qty,
                'affiliate_earnings' => $item->affiliate_earning
            ];
        });
    }
}
