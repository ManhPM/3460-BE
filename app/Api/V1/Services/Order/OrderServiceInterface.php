<?php

namespace App\Api\V1\Services\Order;

use Illuminate\Http\Request;

interface OrderServiceInterface
{
    public function cancel(Request $request);

    public function getBankTransferInfo($orderId);

    public function generateQrImageUrl($order);

    public function updateOrder($orderId, array $data);
}
