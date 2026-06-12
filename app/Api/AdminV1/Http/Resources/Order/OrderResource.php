<?php

namespace App\Api\AdminV1\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'total' => $this->total,
            'status' => $this->status?->value ?? $this->status,
            'status_label' => $this->status ? \App\Enums\Order\OrderStatus::getDescription($this->status->value) : '',
            'payment_status' => $this->payment_status?->value ?? $this->payment_status,
            'payment_status_label' => $this->payment_status ? \App\Enums\Order\PaymentStatus::getDescription($this->payment_status->value) : '',
            'payment_method' => $this->payment_method?->value ?? $this->payment_method,
            'payment_method_label' => $this->payment_method ? \App\Enums\Payment\PaymentMethod::getDescription($this->payment_method->value) : '',
            'shipping_fee' => $this->shipping_fee ?? 0,
            'discount_value' => $this->discount_value ?? 0,
            'discount_code' => $this->discount_code,
            'points' => $this->points ?? 0,
            'points_discount_value' => $this->points_discount_value ?? 0,
            'voucher_shipping_code' => $this->voucher_shipping_code,
            'voucher_shipping_discount_value' => $this->voucher_shipping_discount_value ?? 0,
            'voucher_product_code' => $this->voucher_product_code,
            'voucher_product_discount_value' => $this->voucher_product_discount_value ?? 0,
            'membership_discount_percentage' => $this->membership_discount_percentage ?? 0,
            'membership_discount_value' => $this->membership_discount_value ?? 0,
            'note' => $this->note,
            'shipping_date' => $this->shipping_date,
            'cancel_reason' => $this->cancel_reason,
            'payment_image' => $this->payment_image ? asset($this->payment_image) : null,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->fullname,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                ];
            }),
            'admin' => $this->whenLoaded('admin', function () {
                return [
                    'id' => $this->admin->id,
                    'branch_name' => $this->admin->branch_name,
                    'branch_phone' => $this->admin->branch_phone,
                    'branch_address' => $this->admin->branch_address,
                ];
            }),
            'province' => $this->whenLoaded('province', function () {
                return [
                    'id' => $this->province->id,
                    'name' => $this->province->name,
                ];
            }),
            'ward' => $this->whenLoaded('ward', function () {
                return [
                    'id' => $this->ward->id,
                    'name' => $this->ward->name,
                ];
            }),
            'details' => $this->whenLoaded('details', function () {
                return $this->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product_name,
                        'product_slug' => $detail->product_slug,
                        'product_avatar' => $detail->product_avatar ? asset($detail->product_avatar) : null,
                        'product_variation_id' => $detail->product_variation_id,
                        'unit_price' => $detail->unit_price,
                        'qty' => $detail->qty,
                        'product' => $detail->product ? [
                            'id' => $detail->product->id,
                            'name' => $detail->product->name,
                            'slug' => $detail->product->slug,
                            'avatar' => $detail->product->avatar ? asset($detail->product->avatar) : null,
                            'type' => $detail->product->type?->value ?? $detail->product->type,
                        ] : null,
                        'productVariation' => $detail->productVariation ? [
                            'id' => $detail->productVariation->id,
                            'image' => $detail->productVariation->image ? asset($detail->productVariation->image) : null,
                            'attribute_variations' => $detail->productVariation->attributeVariations?->map(function ($attr) {
                                return [
                                    'id' => $attr->id,
                                    'name' => $attr->name,
                                ];
                            }) ?? [],
                        ] : null,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
