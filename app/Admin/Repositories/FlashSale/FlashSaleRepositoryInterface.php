<?php

namespace App\Admin\Repositories\FlashSale;

use App\Admin\Repositories\EloquentRepositoryInterface;


interface FlashSaleRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFlashSaleInfo($id);
    public function getCurrentFlashSale();
    public function deleteDetail($id);
    public function getAllFlashSaleProducts_Rows($flash_sale_id);
}
