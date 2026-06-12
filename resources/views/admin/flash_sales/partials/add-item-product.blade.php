<tr @class([
    'item-product',
    'product-' . $product->slug,
    'product-variation-' .
    optional($product->product_variation)->id => $product->product_variation,
]) data-product-slug="{{ $product->slug }}"
    data-product-variation-id="{{ optional($product->product_variation)->id }}">
    <td class="align-middle">
        <span class="remove-item-product cursor-pointer"><i class="ti ti-x"></i></span>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @if ($product->type != App\Enums\Product\ProductType::Simple && optional($product->product_variation)->image)
                <img src="{{ asset($product->product_variation->image) }}" alt="" class="rounded me-2"
                    style="width:40px;height:40px;object-fit:cover;">
            @elseif ($product->avatar)
                <img src="{{ asset($product->avatar) }}" alt="" class="rounded me-2"
                    style="width:40px;height:40px;object-fit:cover;">
            @endif
            <div class="min-w-0">
                <x-link :href="route('admin.product.edit', $product->id)" :title="$product->name" />
                @includeUnless(
                    $product->type == App\Enums\Product\ProductType::Simple,
                    'admin.orders.partials.product-variation',
                    [
                        'attribute_variations' =>
                            optional($product->product_variation)->attribute_variations ?? [],
                    ]
                )
            </div>
        </div>
    </td>
    <td class="text-center">
        <x-input type="number" name="qty[]" value="1" min="1" autocomplete="off"
            class="form-control text-center" style="max-width: 6rem; margin: 0 auto;" />
    </td>
    <td class="text-center">
        <span class="badge bg-secondary">0</span>
    </td>
    <x-input type="hidden" name="product_id[]" :value="$product->id" />
    @if ($product->type == App\Enums\Product\ProductType::Simple)
        @php
            // Xử lý lấy giá sản phẩm không biến thể
            $price = $product->is_flash_sale ? $product->flashsale_price : $product->promotion_price;
        @endphp
        <td class="unit-price align-middle text-center">
            <x-input-price id="flashsale_price_{{ $product->id }}" name="flashsale_price[]" :value="$price"
                class="text-center" style="margin: 0 auto;" />
        </td>
        <x-input type="hidden" name="product_variation_id[]" value="" />
        <x-input type="hidden" name="product_variation_flashsale_price[]" value="" />
    @else
        @php
            // Xử lý lấy giá sản phẩm biến thể
            $variationPrice = $product->is_flash_sale
                ? $product->product_variation->flashsale_price
                : $product->product_variation->promotion_price;
        @endphp
        <td class="unit-price align-middle text-center">
            <x-input id="flashsale_price_variation_{{ optional($product->product_variation)->id }}"
                name="product_variation_flashsale_price[]" :value="$variationPrice" class="text-center"
                style="margin: 0 auto;" />
        </td>
        <x-input type="hidden" name="product_variation_id[]" :value="$product->product_variation->id" />
        <x-input type="hidden" name="flashsale_price[]" value="" />
    @endif
</tr>
