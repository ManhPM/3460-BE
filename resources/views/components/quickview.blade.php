<div id="resultQuickViewRequest">
    @if (isset($productModal))
        @php
            $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
            $settings = $settingRepository->getAll();
        @endphp
        <div id="quickViewProductModal" class="modal">
            <div class="modal-dialog modal-dialog-product-preview">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>{{ $productModal->name }}</h4>
                        <span class="close">
                            <i class="ti ti-x"></i>
                        </span>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-5 mb-5 mt-5">
                            <div class="position-relative text-center">
                                <x-input name="hidden_avatar_modal" type="hidden"
                                    value="{{ asset($productModal->avatar) }}" />
                                <div class="fotorama" data-nav="thumbs" data-allowfullscreen="true">
                                    @if ($productModal->gallery)
                                        @foreach ($productModal->gallery as $item)
                                            <img src="{{ asset($item) }}" alt="Product quickview image">
                                        @endforeach
                                    @else
                                        <img src="{{ asset($productModal->avatar ?? 'userfiles/images/no-product.jpg') }}"
                                            alt="Product image">
                                    @endif
                                </div>
                                @if (!isset($productModal->productVariations[0]))
                                    <span
                                        class="badge badge-danger position-absolute end-0 top-0 m-3">{{ round(100 - ($productModal->promotion_price / $productModal->price) * 100) }}%</span>
                                @else
                                    <span
                                        class="badge badge-danger position-absolute end-0 top-0 m-3">{{ round(
                                            100 - ($productModal->productVariations[0]->promotion_price / $productModal->productVariations[0]->price) * 100,
                                        ) }}%</span>
                                @endif
                                @if ($productModal->is_featured == App\Enums\DefaultActiveStatus::Active)
                                    <span class="badge badge-featured position-absolute start-0 top-0 m-3">Nổi
                                        bật</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-7 mb-5 mt-5">
                            <div style="border-bottom: 1px solid #f5f5f5" class="row align-items-center">
                                <div class="col-md-7">
                                    <h3>{{ $productModal->name }}</h3>
                                    <div class="rating">
                                        @for ($i = 1; $i <= $productModal->avg_rating; $i++)
                                            <span class="star">★</span>
                                        @endfor
                                        @for ($i = 5; $i > $productModal->avg_rating; $i--)
                                            <span style="color: gray" class="star">★</span>
                                        @endfor
                                        <span>{{ $productModal->reviews->count() }} khách hàng đánh giá</span>
                                        <span class="ms-2"><strong> Đã bán:</strong>
                                            {{ $productModal->total_sold }}</span>
                                    </div>
                                </div>
                                <div class="col-md-5 justify-content-between align-items-center text-end">
                                    <a class="lead"
                                        href="{{ asset($settings->where('setting_key', 'footer_facebook')->first()->plain_value) }}">
                                        <img src="{{ asset('assets/images/icon-facebook.png') }}" height="30"
                                            width="30" alt=""></a>
                                    <a class="lead me-2 ms-2"
                                        href="{{ asset($settings->where('setting_key', 'footer_tiktok')->first()->plain_value) }}">
                                        <img src="{{ asset('assets/images/icon-tiktok.png') }}" height="30"
                                            width="30" alt=""></a>
                                    <a class="lead me-2 ms-2"
                                        href="{{ asset($settings->where('setting_key', 'footer_linkedin')->first()->plain_value) }}">
                                        <img src="{{ asset('assets/images/icon-linkedin.png') }}" height="30"
                                            width="30" alt=""></a>
                                    <a class="lead me-2 ms-2"
                                        href="{{ asset($settings->where('setting_key', 'footer_youtube')->first()->plain_value) }}">
                                        <img src="{{ asset('assets/images/icon-youtube.png') }}" height="30"
                                            width="30" alt=""></a>
                                    <a class="lead"
                                        href="{{ asset($settings->where('setting_key', 'footer_instagram')->first()->plain_value) }}">
                                        <img src="{{ asset('assets/images/icon-instagram.png') }}" height="30"
                                            width="30" alt=""></a>
                                </div>
                            </div>
                            @if ($productModal->is_flash_sale)
                                @php
                                    $flash_sale = $productModal->is_flash_sale->details
                                        ->where('product_id', '=', $productModal->id)
                                        ->first();
                                @endphp
                                <div class="row align-items-center ms-xl-1 mb-3 mt-3">
                                    <div class="col-md-8 bg-default h-100 text-center text-white">Kết thúc sau
                                        <strong id="countdown-flashsale-product-modal"></strong>
                                    </div>
                                    <div style="background-color: #f5f5f5;" class="col-md-4 text-center">Đã bán :
                                        {{ $flash_sale->sold ?? 0 }}/{{ $flash_sale->qty }}</div>
                                </div>
                            @endif

                            @if (!$productModal->is_contact_price)
                                @if (!isset($productModal->productVariations[0]))
                                    <p class="lead">
                                        <del>{{ format_price($productModal->price) }}</del>
                                        <strong
                                            class="text-red">{{ format_price($productModal->promotion_price) }}</strong><br>
                                        @if (isset($productModal->is_flash_sale))
                                            <span class="flashsale-price">FLASH SALE
                                                - <span
                                                    id="flashsalePrice">{{ format_price($productModal->flashsale_price) }}</span></span>
                                        @endif
                                    </p>
                                @else
                                    <p id="productDetailPrice" class="lead">
                                        <del
                                            id="productVariationPriceModal">{{ format_price($productModal->productVariations[0]->price) }}</del>
                                        <strong id="productVariationPromotionPriceModal"
                                            class="text-red">{{ format_price($productModal->productVariations[0]->promotion_price) }}</strong><br>
                                        @if (isset($productModal->is_flash_sale))
                                            <span class="flashsale-price">FLASH SALE -
                                                <span
                                                    id="flashsalePrice">{{ format_price($productModal->productVariations[0]->flashsale_price) }}</span>
                                            </span>
                                        @endif
                                    </p>
                                @endif
                            @endif

                            <x-input type="hidden" name="hidden_product_id_modal" :value="$productModal->id" />

                            @if (isset($productModal->productVariations[0]))
                                <x-input type="hidden" name="hidden_quantity_modal" />
                                <x-input type="hidden" name="hidden_product_variation_modal_id" />
                                @foreach ($productModal->productAttributes as $item)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span>{{ $item->attribute->name }}: <strong
                                                    id="attribute_variation_name_modal{{ $item->attribute->id }}">Black</strong></span><br>
                                            <x-input id="hiddenAttributeModal" type="hidden"
                                                name="attribute_variation_modal_ids[{{ $item->attribute->id }}]" />
                                            <div class="row me-3 mt-2">
                                                @foreach ($item->attributeVariations as $attributeVariation)
                                                    @if ($item->attribute->type == App\Enums\Attribute\AttributeType::Color)
                                                        <a style="background-color: {{ $attributeVariation->meta_value['color'] }}"
                                                            data-attribute-name="{{ $attributeVariation->name }}"
                                                            data-attribute-id="{{ $item->attribute->id }}"
                                                            data-attribute-variation-id="{{ $attributeVariation->id }}"
                                                            class="col-2 custom-col btn btn-sm square-btn color-btn-modal mb-2 h-16 w-16"></a>
                                                    @else
                                                        <a data-attribute-name="{{ $attributeVariation->name }}"
                                                            data-attribute-id="{{ $item->attribute->id }}"
                                                            data-attribute-variation-id="{{ $attributeVariation->id }}"
                                                            class="col-2 custom-col btn btn-sm square-btn capacity-btn-modal mb-2 w-5">
                                                            <p class="me-2 ms-2 mt-3">{{ $attributeVariation->name }}
                                                            </p>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <span>Trạng thái: <span id="instockModal"
                                        class="text-green">{{ $productModal->productVariations[0]->qty == 0 ? 'Hết' : 'còn ' . $productModal->productVariations[0]->qty }}
                                        Hàng</span></span>
                                <div class="row mt-3">
                                    @if (!$productModal->is_contact_price)
                                        <div class="col-md-3">
                                            <div class="input-group mt-2">
                                                <button disabled id="btnDecrementModal" class="btn btn-default"
                                                    type="button" onclick="decrementDetail()">-</button>
                                                <input readonly onblur="isEnoughQuantity(this)"
                                                    id="filter-input-detail-modal" class="form-control text-center"
                                                    value="1" min="1">
                                                <button disabled id="btnIncrementModal" class="btn btn-default"
                                                    type="button" onclick="incrementDetail()">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4"><button id="btnAddToCartModal" disabled
                                                class="btn btn-default-primary w-100 mt-2"><strong>Thêm vào giỏ
                                                    hàng</strong></button>
                                        </div>
                                        <div class="col-md-3"><button id="btnBuyNowModal" disabled
                                                class="btn btn-default w-100 mt-2"><strong>Mua
                                                    ngay</strong></button></div>
                                    @else
                                        <div onclick="location.href='tel:{{ $settings->firstWhere('setting_key', 'contact_phone')->plain_value }}'"
                                            class="col-md-4"><button class="btn btn-default w-100 mt-2"><strong><i
                                                        class="ti ti-phone-call me-2"></i>Liên hệ
                                                    ngay</strong></button>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <x-input type="hidden" name="hidden_quantity_modal" :value="$productModal->qty" />
                                <span>Trạng thái: <span
                                        class="text-green">{{ $productModal->qty == 0 ? 'Hết' : 'còn ' . $productModal->qty }}
                                        Hàng</span></span>
                                <div class="row mt-3">
                                    @if (!$productModal->is_contact_price)
                                        <div class="col-md-3">
                                            <div class="input-group mt-2">
                                                <button class="btn btn-default" type="button"
                                                    onclick="decrementDetail()">-</button>
                                                <input onblur="isEnoughQuantity(this)" id="filter-input-detail-modal"
                                                    class="form-control text-center" value="1" min="1">
                                                <button class="btn btn-default" type="button"
                                                    onclick="incrementDetail()">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4"><button id="btnAddToCartModal"
                                                class="btn btn-default-primary w-100 mt-2"><strong>Thêm vào giỏ
                                                    hàng</strong></button>
                                        </div>
                                        <div class="col-md-3"><button id="btnBuyNowModal"
                                                class="btn btn-default w-100 mt-2"><strong>Mua
                                                    ngay</strong></button></div>
                                    @else
                                        <div onclick="location.href='tel:{{ $settings->firstWhere('setting_key', 'contact_phone')->plain_value }}'"
                                            class="col-md-4"><button class="btn btn-default w-100 mt-2"><strong><i
                                                        class="ti ti-phone-call me-2"></i>Liên hệ
                                                    ngay</strong></button>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div style="border-top: 1px solid #f5f5f5" class="row mt-5">
                                <p class="mt-2">SKU: {{ $productModal->sku }}</p>
                                <p>Danh mục:
                                    @foreach ($productModal->categories as $item)
                                        <x-link class="text-default" :href="route('user.product.indexUser', ['category_id' => $item->id])">{{ $item->name }}</x-link>
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    $(document).ready(function() {
        let currentHiddenAttributeValuesModal = [];
        document.querySelectorAll('.color-btn-modal').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.color-btn-modal').forEach(btn => btn.classList
                    .remove(
                        'lift-up'));
                this.classList.add('lift-up');
            });
        });
        document.querySelectorAll('.capacity-btn-modal').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.capacity-btn-modal p').forEach(p => p.classList
                    .remove(
                        'bold-text'));
                this.querySelector('p').classList.add('bold-text');
            });
        });

        function updateCountdownModal() {
            const startTime = new Date();
            const endTime = new Date('{{ $productModal->is_flash_sale->end_time ?? 0 }}');

            const diffInMs = endTime - startTime;
            const diffInHours = Math.floor(diffInMs / 3600000);
            const diffInMinutes = Math.floor((diffInMs % 3600000) / 60000);
            const diffInSeconds = Math.floor((diffInMs % 60000) / 1000);
            const diffInMilliseconds = diffInMs % 1000;
            const formattedTime =
                `${diffInHours.toString().padStart(2, '0')} Giờ : ${diffInMinutes.toString().padStart(2, '0')} Phút : ${diffInSeconds.toString().padStart(2, '0')} Giây`;
            document.getElementById('countdown-flashsale-product-modal').textContent = formattedTime;
        }
        const endTime = '{{ $productModal->is_flash_sale->end_time ?? 0 }}';
        if (endTime != 0) {
            updateCountdownModal();
            const countdownInterval = setInterval(updateCountdownModal, 1000);
        }

        $('.color-btn-modal, .capacity-btn-modal').click(function() {
            var attributeName = $(this).data('attribute-name');
            var attributeId = $(this).data('attribute-id');
            var attributeVariationId = $(this).data('attribute-variation-id');
            let hiddenAttributeModalValues = [];
            const elements = document.querySelectorAll("#hiddenAttributeModal");

            $('#attribute_variation_name_modal' + attributeId).text(attributeName);

            $('input[name="attribute_variation_modal_ids[' + attributeId + ']"]').val(
                attributeVariationId);

            $('.color-btn-modal, .capacity-btn-modal').addClass('disabled').css({
                'pointer-events': 'none',
                'opacity': '0.5'
            });


            elements.forEach(element => {
                hiddenAttributeModalValues.push(element.value);
            });
            const hasEmpty = hiddenAttributeModalValues.some(value => value === '');
            if (!hasEmpty && !arraysEqual(currentHiddenAttributeValuesModal,
                    hiddenAttributeModalValues)) {
                currentHiddenAttributeValuesModal = hiddenAttributeModalValues;
                $.ajax({
                    type: "GET",
                    url: '{{ route('user.product.findVariationByAttributeVariationIds') }}',
                    data: {
                        attribute_variation_ids: hiddenAttributeModalValues,
                        product_id: $('input[name="hidden_product_id_modal"]').val()
                    },
                    success: function(response) {
                        $('#productVariationPriceModal').text(response.data.price);
                        $('#flashsalePrice').text(response.data.flashsale_price);
                        $('#productVariationPromotionPriceModal').text(response.data
                            .promotion_price);
                        if (response.data.qty == 0) {
                            $('#instockModal').text(`Hết hàng`);
                        } else {
                            $('#instockModal').text(`còn ${response.data.qty} hàng`);
                            $('input[name="hidden_quantity_modal"]').val(response.data.qty);
                            $('input[name="hidden_product_variation_modal_id"]').val(
                                response
                                .data
                                .id);
                            $('#filter-input-detail-modal').removeAttr('readonly');
                            $('#btnAddToCartModal').removeAttr('disabled');
                            $('#btnBuyNowModal').removeAttr('disabled');
                            $('#btnIncrementModal').removeAttr('disabled');
                            $('#btnDecrementModal').removeAttr('disabled');
                        }
                    },
                    error: function(response) {
                        handleAjaxErrorNew(response);
                    },
                    complete: function() {
                        // Remove "disabled" class and reset CSS to enable the buttons after AJAX completes
                        $('.color-btn-modal, .capacity-btn-modal').removeClass('disabled')
                            .css({
                                'pointer-events': 'auto',
                                'opacity': '1'
                            });
                    }
                })
            } else {
                // Enable buttons if no AJAX call is made
                $('.color-btn-modal, .capacity-btn-modal').removeClass('disabled').css({
                    'pointer-events': 'auto',
                    'opacity': '1'
                });
            }

        });

        $('#btnBuyNowModal').click(function(e) {
            var productId = $('input[name="hidden_product_id_modal"]').val();
            var productVariationId = $('input[name="hidden_product_variation_modal_id"]').val();
            var qty = $('#filter-input-detail-modal').val();
            $.ajax({
                type: "POST",
                url: '{{ route('user.cart.buyNow') }}',
                data: {
                    product_id: productId,
                    product_variation_id: productVariationId,
                    qty: qty,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        window.location.href =
                            `{{ route('user.cart.checkout') }}?cart_id=${response.data.id}&qty=${response.data.qty}`;
                    } else {
                        showToastify('error', 'Lỗi', 'Không thể xử lý đơn hàng của bạn!');
                    }
                },
                error: function(response) {
                    showToastify('error', 'Lỗi', response.responseJSON.data ?
                        `${response.responseJSON.data.message}` :
                        `${response.responseJSON.message}`);
                }
            });
        })
        $('#btnAddToCartModal').click(function(e) {
            var productId = $('input[name="hidden_product_id_modal"]').val();
            var productImageUrl = $('input[name="hidden_avatar_modal"]').val();
            var productVariationId = $('input[name="hidden_product_variation_modal_id"]').val();
            var qty = $('#filter-input-detail-modal').val();
            if (requestDone) {
                requestDone = false;
                $.ajax({
                    type: "POST",
                    url: '{{ route('user.cart.store') }}',
                    data: {
                        product_id: productId,
                        product_variation_id: productVariationId,
                        qty: qty,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#cart-count-mobile').text(response.data.count);
                        $('#cart-count').text(response.data.count);
                        handleAddToCartAnimation(productImageUrl);
                    },
                    error: function(response) {
                        showToastify('error', 'Lỗi',
                        'Thêm sản phẩm vào giỏ hàng thất bại!');
                        handleAjaxErrorNew(response);
                    },
                    complete: function() {
                        loadCartItems();
                        requestDone = true;
                    }
                });
            }

        });
    });

    function incrementDetail() {
        var input = document.getElementById('filter-input-detail-modal');
        var hiddenQuantity = parseInt($('input[name="hidden_quantity_modal"]').val());
        if (parseInt(input.value) + 1 > hiddenQuantity) {
            showToastify('error', 'Lỗi', 'Số lượng vượt quá hàng trong kho!');
            input.value = hiddenQuantity;
        } else {
            input.value = parseInt(input.value) + 1;
        }
    }

    function isEnoughQuantity(input) {
        if (!/^\d+$/.test(input.value)) {
            showToastify('error', 'Lỗi', 'Vui lòng chỉ nhập số!');
            input.value = 1;
        }
        if (input.value <= 0) {
            showToastify('error', 'Lỗi', 'Số lượng phải lớn hơn 0!');
            input.value = 1;
        }
        var hiddenQuantity = parseInt($('input[name="hidden_quantity_modal"]').val());
        if (input.value > hiddenQuantity) {
            showToastify('error', 'Lỗi', `Số lượng vượt quá hàng trong kho, còn lại ${hiddenQuantity} sản phẩm!`);
            input.value = 1;
        }
    }

    function decrementDetail() {
        var input = document.getElementById('filter-input-detail-modal');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>
