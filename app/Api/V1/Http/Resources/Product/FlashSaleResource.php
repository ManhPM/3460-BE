<?php

namespace App\Api\V1\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleResource extends JsonResource
{
    public function toArray($request)
    {
        $keywords = $request->input('keywords');
        $productsQuery = $this->details();

        if ($keywords) {
            $productsQuery->whereHas('product', function ($query) use ($keywords) {
                $query->where('name', 'LIKE', '%' . $keywords . '%');
            });
        }

        // Lấy hết sản phẩm (kể cả biến thể) còn hàng (sold < qty)
        $products = $productsQuery->whereRaw('sold < qty')
            ->get()
            ->pluck('product')
            ->unique('id')
            ->values();

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'products' => new AllProductResource($products),
        ];

        return $data;
    }
}
