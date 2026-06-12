<tr @class([
    'item-product',
    'product-' . $product->slug,
    'product-variation-' .
    optional($product->productVariation)->id => $product->productVariation,
])>
    <td class="align-middle">
        <span class="remove-item-product cursor-pointer"><i class="ti ti-x"></i></span>
        <x-input type="hidden" name="order_detail[id][]" value="0" />
        <x-input type="hidden" name="order_detail[product_slug][]" :value="$product->slug" />
        <x-input type="hidden" name="order_detail[product_variation_id][]" :value="$product->productVariation->id ?? 0" />
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @php
                $isVariation = $product->type != \App\Enums\Product\ProductType::Simple;
                $thumb = $isVariation ? $product->productVariation->image ?? null : $product->avatar ?? null;
            @endphp
            @if ($thumb)
                <img src="{{ asset($thumb) }}" alt="" class="rounded me-2"
                    style="width:40px;height:40px;object-fit:cover;">
            @endif
            <div class="min-w-0">
                <x-link :href="route('admin.product.edit', $product->id)" :title="$product->name" />
                @includeUnless(
                    $product->type == App\Enums\Product\ProductType::Simple,
                    'admin.orders.partials.product-variation',
                    [
                        'attribute_variations' => optional($product->productVariation)->attribute_variations ?? [],
                    ]
                )
            </div>
        </div>
    </td>
    <td class="text-center">
        <x-input type="number" name="order_detail[product_qty][]" value="1" min="1" autocomplete="off"
            class="form-control form-control-sm text-center" style="max-width: 6rem;" />
    </td>
    @if ($product->type == App\Enums\Product\ProductType::Simple)
        @php
            // Xử lý lấy giá sản phẩm không biến thể
            $price = $product->is_flash_sale ? $product->flashsale_price : $product->promotion_price;
        @endphp
        <td class="unit-price align-middle text-end">
            {{ format_price_no_currency($price) }}</td>
        <td class="total-price align-middle text-end">
            {{ format_price_no_currency($price) }}</td>
        <x-input type="hidden" name="order_detail[unit_price][]" :value="$price" />
    @else
        @php
            // Xử lý lấy giá sản phẩm biến thể
            $variationPrice = $product->is_flash_sale
                ? $product->productVariation->flashsale_price
                : $product->productVariation->promotion_price;
        @endphp
        <td class="unit-price align-middle text-end">
            {{ format_price_no_currency($variationPrice) }}
        </td>
        <td class="total-price align-middle text-end">
            {{ format_price_no_currency($variationPrice) }}
        </td>
        <x-input type="hidden" name="order_detail[unit_price][]" :value="$variationPrice" />
    @endif
</tr>
