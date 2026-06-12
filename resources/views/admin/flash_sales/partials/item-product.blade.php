<tr @class([
    'item-product',
    'product-' . $flash_sale_detail['product']['slug'],
    'product-variation-' .
    ($flash_sale_detail['product_variation']['id'] ?? '') => isset(
        $flash_sale_detail['product_variation']['id']),
]) data-product-slug="{{ $flash_sale_detail['product']['slug'] }}"
    data-product-variation-id="{{ $flash_sale_detail['product_variation']['id'] ?? '' }}">
    <td class="align-middle">
        <span class="remove-item-product cursor-pointer" data-id="{{ $flash_sale_detail->id }}"><i
                class="ti ti-x"></i></span>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @php
                $isVariation = $flash_sale_detail['product']['type'] != \App\Enums\Product\ProductType::Simple;
                $thumb = $isVariation
                    ? $flash_sale_detail['product_variation']['image'] ?? null
                    : $flash_sale_detail['product']['avatar'] ?? null;
            @endphp
            @if ($thumb)
                <img src="{{ asset($thumb) }}" alt="" class="rounded me-2"
                    style="width:40px;height:40px;object-fit:cover;">
            @endif
            <div class="min-w-0">
                @if (isset($flash_sale_detail['product']['id']))
                    <x-link :href="route('admin.product.edit', $flash_sale_detail['product']['id'])" :title="$flash_sale_detail['product']['name']" />
                @else
                    {{ $flash_sale_detail['product']['name'] }}
                @endif
                @includeUnless(
                    $flash_sale_detail['product']['type'] == \App\Enums\Product\ProductType::Simple,
                    'admin.orders.partials.product-variation',
                    [
                        'attribute_variations' =>
                            $flash_sale_detail['product_variation']['attribute_variations'] ?? [],
                    ]
                )
            </div>
        </div>
    </td>
    <td class="text-center">
        <x-input type="number" name="qty[]" :value="$flash_sale_detail->qty" min="1" autocomplete="off"
            class="form-control text-center" style="max-width: 6rem; margin: 0 auto;" :data-parsley-number-message="__('Trường này phải là số.')"
            :data-parsley-min-message="__('Giá trị phải lớn hơn 1.')" />
    </td>
    <td class="text-center">
        <span class="badge bg-info">{{ $flash_sale_detail->sold ?? 0 }}</span>
    </td>
    <x-input type="hidden" name="product_id[]" :value="$flash_sale_detail['product']['id']" />
    @if ($flash_sale_detail['product']['type'] == \App\Enums\Product\ProductType::Simple)
        <td class="unit-price align-middle text-center">
            <x-input id="flashsale_price_{{ $flash_sale_detail['product']['id'] }}" name="flashsale_price[]"
                :value="$flash_sale_detail->flashsale_price" class="text-center" style="margin: 0 auto;" />
        </td>
        <x-input type="hidden" name="product_variation_id[]" value="" />
        <x-input type="hidden" name="product_variation_flashsale_price[]" value="" />
    @else
        <td class="unit-price align-middle text-center">
            <x-input id="flashsale_price_variation_{{ $flash_sale_detail['product_variation']['id'] ?? '' }}"
                name="product_variation_flashsale_price[]" :value="$flash_sale_detail->flashsale_price" class="text-center"
                style="margin: 0 auto;" />
        </td>
        <x-input type="hidden" name="product_variation_id[]" :value="$flash_sale_detail['product_variation']['id'] ?? 0" />
        <x-input type="hidden" name="flashsale_price[]" value="" />
    @endif
</tr>
