@php
    $isEdit = isset($order) && isset($order->id) && $order->id;
@endphp

<tr @class([
    'item-product',
    'product-' . $order_detail['product_slug'],
    'product-variation-' .
    ($order_detail['productVariation']['id'] ?? '') => isset(
        $order_detail['productVariation']['id']),
]) data-product-slug="{{ $order_detail['product_slug'] }}"
    data-product-variation-id="{{ $order_detail['productVariation']['id'] ?? '' }}">
    <td class="align-middle">
        @if (!$isEdit)
            <span class="remove-item-product cursor-pointer" data-id="{{ $order_detail->id }}"><i class="ti ti-x"></i></span>
        @else
            <span class="text-muted"><i class="ti ti-lock"></i></span>
        @endif
        <x-input type="hidden" name="order_detail[id][]" :value="$order_detail->id" />
        <x-input type="hidden" name="order_detail[product_slug][]" :value="$order_detail['product_slug']" />
        <x-input type="hidden" name="order_detail[product_variation_id][]" :value="$order_detail['productVariation']['id'] ?? 0" />
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @php
                $isVariation = $order_detail['product']['type'] != \App\Enums\Product\ProductType::Simple;
                $thumb = $isVariation
                    ? $order_detail['productVariation']['image'] ?? null
                    : $order_detail['product']['avatar'] ?? null;
            @endphp
            @if ($thumb)
                <img src="{{ asset($thumb) }}" alt="" class="rounded me-2"
                    style="width:40px;height:40px;object-fit:cover;">
            @endif
            <div class="min-w-0">
                @if (isset($order_detail['product']['id']))
                    <x-link :href="route('admin.product.edit', $order_detail['product']['id'])" :title="$order_detail['product_name']" />
                @else
                    {{ $order_detail['product_name'] }}
                @endif
                @includeUnless(
                    $order_detail['product']['type'] == \App\Enums\Product\ProductType::Simple,
                    'admin.orders.partials.product-variation',
                    [
                        'attribute_variations' => $order_detail['productVariation']['attribute_variations'] ?? [],
                    ]
                )
            </div>
        </div>
    </td>
    <td class="text-center">
        @if (!$isEdit)
            <x-input type="number" name="order_detail[product_qty][]" :value="$order_detail->qty" min="1" autocomplete="off"
                class="form-control form-control-sm text-center" style="max-width: 6rem;" :data-parsley-number-message="__('Trường này phải là số.')"
                :data-parsley-min-message="__('Giá trị phải lớn hơn 1.')" />
        @else
            <span class="fw-semibold">{{ $order_detail->qty }}</span>
            <x-input type="hidden" name="order_detail[product_qty][]" :value="$order_detail->qty" />
        @endif
    </td>
    <td class="unit-price align-middle text-end">{{ format_price_no_currency($order_detail->unit_price) }}</td>
    <td class="total-price align-middle text-end">
        {{ format_price_no_currency($order_detail->unit_price * $order_detail->qty) }}
    </td>
    <x-input type="hidden" name="order_detail[unit_price][]" :value="$order_detail->unit_price" />
</tr>
