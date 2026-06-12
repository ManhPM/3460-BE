<div class="card hover-shadow border-0">
    <div class="position-relative">
        <img onclick="location.href='{{ route('user.product.detail', ['slug' => $item->slug]) }}';"
            class="card-img-top img-default" src="{{ asset($item->avatar ?? 'userfiles/images/no-product.jpg') }}"
            style="cursor: pointer;" alt="Product 3">
        <img onclick="location.href='{{ route('user.product.detail', ['slug' => $item->slug]) }}';"
            class="card-img-top img-hover" src="{{ asset($item->gallery[0] ?? 'userfiles/images/no-product.jpg') }}"
            alt="Product 3" style="display: none;cursor: pointer;">
        @if (!$item->is_contact_price)
            @if ($item->type == \App\Enums\Product\ProductType::Simple)
                <span class="badge badge-danger position-absolute end-0 top-0 m-3 text-white">
                    {{ $item->price != 0 ? ceil(100 - (($item->is_flash_sale ? $item->flashsale_price : $item->promotion_price) * 100) / $item->price) . '%' : '' }}
                </span>
            @else
                <span class="badge badge-danger position-absolute end-0 top-0 m-3 text-white">
                    @php
                        $minPrice = $item->productVariations->min('price');
                        $minSalePrice = $item->productVariations->min(
                            $item->is_flash_sale ? 'flashsale_price' : 'promotion_price',
                        );
                    @endphp
                    {{ $minPrice != 0 ? ceil(100 - ($minSalePrice * 100) / $minPrice) . '%' : '' }}
                </span>
            @endif
        @endif
        @if ($item->is_featured == App\Enums\DefaultActiveStatus::Active)
            <span class="badge badge-featured position-absolute start-0 top-0 m-3">Nổi bật</span>
        @endif
    </div>
    <div class="card-body">
        <h6 class="card-title mb-1">
            <x-link class="text-black" :href="route('user.product.detail', ['slug' => $item->slug])">
                {{ $item->name }}
            </x-link>
        </h6>
        <div class="rating fs-12 {{ $item->is_contact_price ? 'opacity-0' : '' }}">
            @for ($i = 1; $i <= 5; $i++)
                <span class="star" style="color: {{ $i <= $item->avg_rating ? '#ffa200' : '#ccc' }};">★</span>
            @endfor
            <span>{{ $item->reviews->count() }}</span>
        </div>
        @if ($item->type == \App\Enums\Product\ProductType::Simple)
            @if ($item->is_contact_price)
                <p class="text-price">
                    <del class="opacity-0">{{ format_price($item->price) }}</del><br>
                    <strong class="text-red">
                        Liên hệ
                    </strong>
                </p>
                <div onclick="location.href='{{ route('user.product.detail', ['slug' => $item->slug]) }}'"
                    class="product-hover text-center opacity-0">
                    <a style="cursor: pointer;" class="add-to-cart d-flex justify-content-center">
                        <i class="fa fa-shopping-cart w-50" aria-hidden="true"></i>
                        <i class="fa fa-arrows-alt w-50" aria-hidden="true"></i>
                    </a>
                </div>
            @else
                <p class="text-price">
                    <del>{{ format_price($item->price) }}</del><br>
                    <strong class="text-red">
                        {{ format_price($item->is_flash_sale ? $item->flashsale_price : $item->promotion_price) }}
                    </strong>
                </p>
                <div class="product-hover text-center">
                    <a style="cursor: pointer;" class="add-to-cart d-flex justify-content-center">
                        <i onclick="addToCart({{ $item->id }}, '{{ asset($item->avatar) }}')"
                            class="fa fa-shopping-cart w-50" aria-hidden="true"></i>
                        <i class="fa fa-arrows-alt w-50" onclick="showDetailProductModal(this, {{ $item->id }})"
                            aria-hidden="true"></i>
                    </a>
                </div>
            @endif
        @else
            @if ($item->is_contact_price)
                <p class="text-price">
                    <strong class="text-red">
                        Liên hệ
                    </strong>
                </p>
            @else
                <p class="text-price">
                    <strong class="text-red">
                        {{ format_price($item->productVariations()->min($item->is_flash_sale ? 'flashsale_price' : 'promotion_price')) }}
                        - <br>
                        {{ format_price($item->productVariations()->max($item->is_flash_sale ? 'flashsale_price' : 'promotion_price')) }}
                    </strong>
                </p>
                <div class="product-hover text-center">
                    <a style="cursor: pointer;" class="add-to-cart d-flex justify-content-center">
                        <i onclick="location.href='{{ route('user.product.detail', ['slug' => $item->slug]) }}'"
                            class="fa fa-shopping-cart w-50" aria-hidden="true"></i>
                        <i class="fa fa-arrows-alt w-50" onclick="showDetailProductModal(this, {{ $item->id }})"
                            aria-hidden="true"></i>
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>
