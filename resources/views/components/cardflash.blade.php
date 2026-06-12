@props(['item'])
</style>
<div class="card hover-shadow border-0 shadow-sm">
    <div class="position-relative">
        <img onclick="location.href='{{ route('user.product.detail', ['slug' => $item->product->slug]) }}';"
            class="card-img-top img-default"
            src="{{ asset($item->product->avatar ?? 'userfiles/images/no-product.jpg') }}" style="cursor: pointer;"
            alt="Product 3">
        <img onclick="location.href='{{ route('user.product.detail', ['slug' => $item->product->slug]) }}';"
            class="card-img-top img-hover"
            src="{{ asset($item->product->gallery[0] ?? 'userfiles/images/no-product.jpg') }}" alt="Product 3"
            style="display: none;cursor: pointer;">
        @if (!$item->product->is_contact_price)
            @if ($item->product->type == \App\Enums\Product\ProductType::Simple)
                <span
                    class="badge badge-danger position-absolute end-0 top-0 m-3 text-white">{{ $item->product->price != 0
                        ? ceil(100 - ($item->product->flashsale_price * 100) / $item->product->price) . '%'
                        : '' }}</span>
            @else
                <span
                    class="badge badge-danger position-absolute end-0 top-0 m-3 text-white">{{ $item->product->productVariations[0]->price != 0
                        ? ceil(
                                100 -
                                    ($item->product->productVariations[0]->promotion_price * 100) /
                                        $item->product->productVariations[0]->price,
                            ) . '%'
                        : '' }}</span>
            @endif
        @endif
        @if ($item->product->is_featured == App\Enums\DefaultActiveStatus::Active)
            <span class="badge badge-featured position-absolute start-0 top-0 m-3">Nổi bật</span>
        @endif
    </div>
    <div class="card-body">
        <h6 class="card-title">
            <x-link class="text-black" :href="route('user.product.detail', ['slug' => $item->product->slug])">
                {{ $item->product->name }}
            </x-link>
        </h6>
        <div class="rating fs-12 {{ $item->product->is_contact_price ? 'opacity-0' : '' }}">
            @for ($i = 1; $i <= 5; $i++)
                <span class="star"
                    style="color: {{ $i <= $item->product->avg_rating ? '#ffa200' : '#ccc' }};">★</span>
            @endfor
            <span>{{ $item->product->reviews->count() }}</span>
        </div>
        @if ($item->product->is_contact_price)
            <p class="text-price"><del class="{{ $item->product->is_contact_price ? 'opacity-0' : '' }}">123</del><br>
                <strong class="text-red">Liên hệ</strong>
            </p>
            <div class="progress-container">
                <div style="width: {{ (($item->qty - $item->sold) / $item->qty) * 100 }}%" class="progress-bar"
                    id="progressBar"></div>
                <div class="progress-content">
                    <div class="progress-icon">
                        <i class="fa fa-bolt"></i>
                    </div>
                    <span id="progressText">Sold: {{ $item->sold ?? 0 }}/{{ $item->qty }}</span>
                </div>
            </div>
        @else
            @if ($item->product->type == \App\Enums\Product\ProductType::Simple)
                <p class="text-price"><del>{{ format_price($item->product->price) }}</del><br><strong
                        class="text-red">{{ format_price($item->product->flashsale_price) }}</strong></p>
            @else
                <p class="text-price"><strong
                        class="text-red">{{ format_price($item->product->productVariations()->min('flashsale_price')) }}
                        - <br>{{ format_price($item->product->productVariations()->max('flashsale_price')) }}</strong>
                </p>
            @endif
            <div class="progress-container">
                <div style="width: {{ (($item->qty - $item->sold) / $item->qty) * 100 }}%" class="progress-bar"
                    id="progressBar"></div>
                <div class="progress-content">
                    <div class="progress-icon">
                        <i class="fa fa-bolt"></i>
                    </div>
                    <span id="progressText">Sold: {{ $item->sold ?? 0 }}/{{ $item->qty }}</span>
                </div>
            </div>
            <div class="product-hover text-center">
                <a style="cursor: pointer;" class="add-to-cart-flash">
                    @if ($item->product->type == \App\Enums\Product\ProductType::Simple)
                        <i onclick="addToCart({{ $item->product->id }}, '{{ asset($item->product->avatar) }}')"
                            class="fa fa-shopping-cart w-50" aria-hidden="true"></i><i class="fa fa-arrows-alt w-50"
                            onclick="showDetailProductModal(this, {{ $item->product->id }})" aria-hidden="true"></i>
                    @else
                        <i onclick="location.href='{{ route('user.product.detail', ['slug' => $item->product->slug]) }}'"
                            class="fa fa-shopping-cart w-50" aria-hidden="true"></i><i class="fa fa-arrows-alt w-50"
                            onclick="showDetailProductModal(this, {{ $item->product->id }})" aria-hidden="true"></i>
                    @endif
                </a>
            </div>
        @endif
    </div>
</div>
