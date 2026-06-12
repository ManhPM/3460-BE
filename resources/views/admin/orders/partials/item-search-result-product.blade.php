<div class="list-group-item py-3 px-3">
    <div class="row align-items-center g-2">
        <div class="col-auto">
            <img class="avatar rounded" src="{{ asset($product->avatar) }}" alt=""
                style="width:48px;height:48px;object-fit:cover;">
        </div>
        <div class="col text-truncate d-flex justify-content-between align-items-center">
            <div>
                <span class="product-name fw-medium">{{ $product->name }}</span>
                @if ($product->type == App\Enums\Product\ProductType::Simple)
                    <small>
                        (
                        @if ($product->promotion_price)
                            {{ format_price($product->promotion_price) }}
                            <span> - </span>
                            <s>{{ format_price($product->price) }}</s>
                        @else
                            {{ format_price($product->price) }}
                        @endif
                        )
                    </small>
                @endif
            </div>
            @if ($product->type == App\Enums\Product\ProductType::Simple)
                <x-button type="button" class="add-product btn-sm btn-outline-primary" :data-product-slug="$product->slug">
                    <i class="ti ti-plus"></i>
                    {{ __('Thêm') }}
                </x-button>
            @endif
        </div>
        @if ($product->type == App\Enums\Product\ProductType::Variable)
            <ul class="product-variations list-unstyled mt-2 pt-2 mb-0 border-top">
                @foreach ($product->productVariations as $product_variation)
                    <li class="py-1">
                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100 overflow-hidden">
                            <div class="d-flex align-items-center min-w-0 flex-grow-1 flex-wrap gap-2">
                                @if (!empty($product_variation->image))
                                    <img class="avatar rounded me-2 flex-shrink-0"
                                        src="{{ asset($product_variation->image) }}" alt=""
                                        style="width:32px;height:32px;object-fit:cover;">
                                @endif
                                <div class="d-flex align-items-center flex-wrap gap-1 min-w-0">
                                    @foreach ($product_variation->attribute_variations as $attribute_variation)
                                        <span
                                            class="badge bg-light text-secondary border fw-normal me-1 mb-1">{{ $attribute_variation->attribute->name . ': ' . $attribute_variation->name }}</span>
                                    @endforeach
                                </div>
                                <span class="flex-shrink-0 ms-2">
                                    @if ($product_variation->promotion_price)
                                        {{ format_price($product_variation->promotion_price) }} <span>-</span>
                                        <s>{{ format_price($product_variation->price) }}</s>
                                    @else
                                        {{ format_price($product_variation->price) }}
                                    @endif
                                </span>
                            </div>
                            <x-button type="button"
                                class="add-product-variation btn-sm btn-outline-primary flex-shrink-0" :data-product-slug="$product->slug"
                                :data-product-variation-id="$product_variation->id">
                                <i class="ti ti-plus"></i>
                                {{ __('Thêm') }}
                            </x-button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
