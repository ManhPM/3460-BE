<?php

namespace App\Admin\Repositories\FlashSale;

use App\Admin\Repositories\EloquentRepository;
use App\Models\FlashSale;
use App\Models\FlashSaleDetail;

class FlashSaleRepository extends EloquentRepository implements FlashSaleRepositoryInterface
{
    public function getModel(): string
    {
        return FlashSale::class;
    }

    public function getFlashSaleInfo($id)
    {
        $detail = FlashSale::find($id);
        return $detail;
    }

    public function getCurrentFlashSale()
    {
        $current_day = date('Y-m-d H:i:s');
        $flashSale = FlashSale::where('start_time', '<=', $current_day)->where('end_time', '>=', $current_day)->where('is_active', 1)->first();
        return $flashSale;
    }

    public function deleteDetail($id)
    {
        $detail = FlashSaleDetail::find($id);
        if ($detail) {
            $detail->delete();
            return true;
        }
        return false;
    }

    public function getAllFlashSaleProducts_Rows($flash_sale_id)
    {
        $detail = FlashSaleDetail::where('flash_sale_id', $flash_sale_id)->get();
        if ($detail) {
            return $detail;
        }
    }
}
