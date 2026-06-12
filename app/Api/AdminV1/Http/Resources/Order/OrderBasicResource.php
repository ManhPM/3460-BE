<?php

namespace App\Api\AdminV1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderBasicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'total' => $this->total,
            'status' => $this->status?->value ?? $this->status,
            'status_label' => $this->status ? \App\Enums\Order\OrderStatus::getDescription($this->status->value) : '',
            'payment_status' => $this->payment_status?->value ?? $this->payment_status,
            'payment_status_label' => $this->payment_status ? \App\Enums\Order\PaymentStatus::getDescription($this->payment_status->value) : '',
            'payment_method' => $this->payment_method?->value ?? $this->payment_method,
            'payment_method_label' => $this->payment_method ? \App\Enums\Payment\PaymentMethod::getDescription($this->payment_method->value) : '',
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->fullname,
                    'email' => $this->user->email,
                ];
            }),
            'admin' => $this->whenLoaded('admin', function () {
                return [
                    'id' => $this->admin->id,
                    'branch_name' => $this->admin->branch_name,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
