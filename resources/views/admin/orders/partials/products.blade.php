@php
    $isEdit = isset($order) && isset($order->id) && $order->id;
@endphp

@if (!$isEdit)
    <div class="d-flex justify-content-between align-items-center mb-2">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
            <i class="ti ti-plus"></i> {{ __('Thêm sản phẩm') }}
        </button>
        <div class="text-muted small">{{ __('Quản lý sản phẩm trong đơn hàng') }}</div>
    </div>
@else
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="text-muted small">{{ __('Chi tiết sản phẩm trong đơn hàng') }}</div>
    </div>
@endif
<style>
    #tableProduct th,
    #tableProduct td {
        text-align: center !important;
    }
</style>
<table id="tableProduct" class="table table-sm align-middle text-center">
    <thead>
        <tr>
            <th class="text-center" style="width: 1%;"></th>
            <th class="text-center" style="width: 55%;">{{ __('Sản phẩm') }}</th>
            <th class="text-center" style="width: 18%;">{{ __('Số lượng') }}</th>
            <th class="text-center" style="width: 12%;">{{ __('Đơn giá') }}</th>
            <th class="text-center" style="width: 12%;">{{ __('Tổng tiền') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($order->details ?? [] as $order_detail)
            @include('admin.orders.partials.item-product', [
                'order_detail' => $order_detail,
                'order' => $order ?? null,
            ])
        @empty
            @include('admin.orders.partials.no-item-product')
        @endforelse
    </tbody>
</table>
@php
    $orderTotal = $order->total ?? 0;
    $orderShippingFee = $order->shipping_fee ?? 0;
    $orderDiscountValue = $order->discount_value ?? 0;
    $orderVoucherShipping = $order->voucher_shipping_discount_value ?? 0;
    $orderVoucherProduct = $order->voucher_product_discount_value ?? 0;
    $orderPointsDiscountValue = $order->points_discount_value ?? 0;
    $orderMembershipDiscountValue = $order->membership_discount_value ?? 0;
    $orderFinalTotal =
        $orderTotal +
        $orderShippingFee -
        $orderDiscountValue -
        $orderVoucherShipping -
        $orderVoucherProduct -
        $orderPointsDiscountValue -
        $orderMembershipDiscountValue;
@endphp
@include('admin.orders.partials.total', [
    'total' => $orderTotal,
    'discountValue' => $orderDiscountValue,
    'points' => $order->points ?? 0,
    'shipping_fee' => $orderShippingFee,
    'voucher_shipping_discount_value' => $orderVoucherShipping,
    'voucher_product_discount_value' => $orderVoucherProduct,
    'points_discount_value' => $orderPointsDiscountValue,
    'membership_discount_value' => $orderMembershipDiscountValue,
    'final_total' => $orderFinalTotal,
])
