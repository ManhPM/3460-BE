<?php

namespace App\Api\V1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Setting\SettingGroup;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Api\V1\Services\Order\OrderServiceInterface;
use App\Traits\HasRepositoryFromAdmin;

class ShowOrderResource extends JsonResource
{
    use AuthSupport, HasRepositoryFromAdmin;

    public function toArray($request)
    {
        $data = [
            'id' => $this->id,

            'points_discount_value' => $this->points_discount_value,
            'points' => $this->points,
            'points_earned' => $this->points_earned,
            'member_ship_points_earned' => $this->member_ship_points_earned ?? 0,
            'membership_discount_percentage' => $this->membership_discount_percentage ?? 0,
            'membership_discount_value' => $this->membership_discount_value ?? 0,

            'customer_fullname' => $this->fullname,
            'customer_phone' => $this->phone,
            'customer_email' => $this->email,
            'shipping_address' => $this->address,
            'shipping_date' => $this->shipping_date,
            'note' => $this->note,
            'total' => $this->total,
            'voucher_shipping_code' => $this->voucher_shipping_code ?? null,
            'voucher_shipping_discount_value' => $this->voucher_shipping_discount_value ?? 0,
            'voucher_product_code' => $this->voucher_product_code ?? null,
            'voucher_product_discount_value' => $this->voucher_product_discount_value ?? 0,
            'discount_code' => $this->discount_code ?? null,
            'discount_value' => $this->discount_value ?? 0,
            'shipping_fee' => $this->shipping_fee ?? 0,
            'code' => $this->code,
            'qr_image' => $this->getQrImage(),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_image' => $this->payment_image ? asset($this->payment_image) : null,
            'created_at' => $this->created_at,
            'province' => $this->province->name,
            'ward' => $this->ward->name,
            'order_details' => $this->details->map(function ($detail) {
                return new ShowOrderDetailResource($detail);
            }),
        ];

        return $data;
    }

    /**
     * Get QR image URL - auto generate if banking payment
     */
    private function getQrImage()
    {
        // Nếu đã có qr_image trong database, trả về
        if ($this->qr_image) {
            return $this->qr_image;
        }

        // Nếu là banking payment và có bank_id, tự động generate QR
        if ($this->payment_method == PaymentMethod::Banking->value && $this->bank_id) {
            try {
                $orderService = app(OrderServiceInterface::class);
                return $orderService->generateQrImageUrl($this->resource);
            } catch (\Exception $e) {
                // Nếu có lỗi, trả về null
                return null;
            }
        }

        return null;
    }
}
