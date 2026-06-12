@isset($sections)
    @foreach ($sections as $section)
        @php
            $loopIndex = $loop->index;
        @endphp
        <div id="container-sale-off" class="position-relative d-flex mt-3">
            @if ($section->is_rightside)
                <div id="product-category" class="rounded-3 container shadow">
                    <div class="row">
                        <div
                            class="col-12 header-box d-flex align-items-center nav-tabs-wrapper rounded-top bg-white shadow-sm">
                            <h5 class="mb-0">
                                {{ $section->title }}
                            </h5>
                            <nav>
                                <div class="nav nav-tabs border-0" id="nav-tab" role="tablist"
                                    style="overflow-x: auto; white-space: nowrap;">
                                    @foreach ($section->categories as $category)
                                        <button class="nav-link tab-btn {{ $loop->first ? 'active' : '' }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#nav-home-category{{ $loopIndex }}-{{ $category->id }}"
                                            type="button" role="tab" aria-controls="nav-sport"
                                            aria-selected="false">{{ $category->name }}</button>
                                    @endforeach
                                </div>
                            </nav>
                        </div>
                        <div class="col-12 col-md-10">
                            <div class="tab-content" id="nav-tabContent">
                                @foreach ($section->categories as $category)
                                    <div id="nav-home-category{{ $loopIndex }}-{{ $category->id }}"
                                        class="tab-pane fade show {{ $loop->first ? 'active' : '' }}" role="tabpanel"
                                        aria-labelledby="nav-home-tab">
                                        <div id="productCarousel-{{ $loopIndex }}-{{ $category->id }}"
                                            class="product-carousel-home carousel slide">
                                            <div class="carousel-inner">
                                                @php
                                                    $products = $category->products->take(8); // Lấy tối đa 8 sản phẩm
                                                    $chunks = $products->chunk(4); // Chia sản phẩm thành các nhóm 4
                                                @endphp
                                                @foreach ($chunks as $index => $chunk)
                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                        <div class="container">
                                                            <div class="row">
                                                                @foreach ($chunk as $product)
                                                                    <div class="col-md-3 col-6 mb-4">
                                                                        <x-cardproduct :item="$product" />
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if ($chunks->count() > 1)
                                                <button class="carousel-control-prev left-btn-slider" type="button"
                                                    data-bs-target="#productCarousel-{{ $loopIndex }}-{{ $category->id }}"
                                                    data-bs-slide="prev">
                                                    <i class="fa fa-chevron-left" aria-hidden="true"></i>
                                                </button>
                                                <button class="carousel-control-next right-btn-slider" type="button"
                                                    data-bs-target="#productCarousel-{{ $loopIndex }}-{{ $category->id }}"
                                                    data-bs-slide="next">
                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <a href="{{ route('user.product.indexUser', ['category_ids[]' => $category->slug]) }}"
                                                    class="text-default w-100"><strong>Tất
                                                        cả sản phẩm</strong></a>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 col-md-2 p-0">
                            <a href="#" class="banner-img">
                                <img loading="lazy" decoding="async" src="{{ asset($section->avatar) }}"
                                    class="d-none d-xl-inline-block" alt="" width="220" height="397">
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div id="product-category" class="rounded-3 container shadow">
                    <div class="row">
                        <div
                            class="col-12 header-box d-flex align-items-center nav-tabs-wrapper rounded-top bg-white shadow-sm">
                            <h5 class="mb-0">
                                {{ $section->title }}
                            </h5>
                            <nav>
                                <div class="nav nav-tabs border-0" id="nav-tab" role="tablist"
                                    style="overflow-x: auto; white-space: nowrap;">
                                    @foreach ($section->categories as $category)
                                        <button class="nav-link tab-btn {{ $loop->first ? 'active' : '' }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#nav-home-category{{ $loopIndex }}-{{ $category->id }}"
                                            type="button" role="tab" aria-controls="nav-sport"
                                            aria-selected="false">{{ $category->name }}</button>
                                    @endforeach
                                </div>
                            </nav>
                        </div>
                        <div class="col-12 col-md-2 p-0">
                            <a href="#" class="banner-img">
                                <img loading="lazy" decoding="async" src="{{ asset($section->avatar) }}"
                                    class="d-none d-xl-inline-block" alt="" width="220" height="397">
                            </a>
                        </div>
                        <div class="col-12 col-md-10">
                            <div class="tab-content" id="nav-tabContent">
                                @foreach ($section->categories as $category)
                                    <div id="nav-home-category{{ $loopIndex }}-{{ $category->id }}"
                                        class="tab-pane fade show {{ $loop->first ? 'active' : '' }}" role="tabpanel"
                                        aria-labelledby="nav-home-tab">
                                        <div id="productCarousel-{{ $loopIndex }}-{{ $category->id }}"
                                            class="product-carousel-home carousel slide">
                                            <div class="carousel-inner">
                                                @php
                                                    $products = $category->products->take(8); // Lấy tối đa 8 sản phẩm
                                                    $chunks = $products->chunk(4); // Chia sản phẩm thành các nhóm 4
                                                @endphp
                                                @foreach ($chunks as $index => $chunk)
                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                        <div class="container">
                                                            <div class="row">
                                                                @foreach ($chunk as $product)
                                                                    <div class="col-md-3 col-6 mb-4">
                                                                        <x-cardproduct :item="$product" />
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if ($chunks->count() > 1)
                                                <button class="carousel-control-prev left-btn-slider" type="button"
                                                    data-bs-target="#productCarousel-{{ $loopIndex }}-{{ $category->id }}"
                                                    data-bs-slide="prev">
                                                    <i class="fa fa-chevron-left" aria-hidden="true"></i>
                                                </button>
                                                <button class="carousel-control-next right-btn-slider" type="button"
                                                    data-bs-target="#productCarousel-{{ $loopIndex }}-{{ $category->id }}"
                                                    data-bs-slide="next">
                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <a href="{{ route('user.product.indexUser', ['category_ids[]' => $category->slug]) }}"
                                                    class="text-default w-100"><strong>Tất
                                                        cả sản phẩm</strong></a>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
@endisset
