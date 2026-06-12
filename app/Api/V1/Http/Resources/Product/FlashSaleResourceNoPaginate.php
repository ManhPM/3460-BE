<?php

namespace App\Api\V1\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleResourceNoPaginate extends JsonResource
{
    public function toArray($request)
    {
        $keywords = $request->input('keywords');
        $detailsQuery = $this->details();

        if ($keywords) {
            $detailsQuery->whereHas('product', function ($query) use ($keywords) {
                $query->where('name', 'LIKE', '%' . $keywords . '%');
            });
        }

        // Get all flash sale details — lấy hết (kể cả biến thể) còn hàng (sold < qty)
        $details = $detailsQuery->with(['product', 'product_variation'])
            ->whereRaw('sold < qty')
            ->get();

        // Group by product_id to get unique products with their variations
        $groupedProducts = $details->groupBy('product_id')->map(function ($productDetails) {
            $firstDetail = $productDetails->first();
            $product = $firstDetail->product;

            // Attach flash sale details to the product
            $product->flash_sale_details = $productDetails;

            return $product;
        })->values();

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'products' => new AllProductFlashSaleResource($groupedProducts),
        ];

        return $data;
    }
}
