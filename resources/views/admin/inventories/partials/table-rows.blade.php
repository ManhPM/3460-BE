@if ($products->isEmpty())
    <tr>
        <td colspan="4" class="text-center text-muted py-5">{{ __('Không có dữ liệu') }}</td>
    </tr>
@else
    @foreach ($products as $product)
        @php
            $simpleKey = ($product->id ?: 0) . ':0';
            $simpleQty = optional($inventoryRows->get($simpleKey)[0] ?? null)->qty ?? 0;
            $hasVariation = $product->productVariations->count() > 0;
        @endphp
        @if (!$hasVariation)
            <tr>
                <td>
                    @if ($product->avatar)
                        <img src="{{ asset($product->avatar) }}" alt="{{ $product->name }}"
                            style="width:36px; height:36px; object-fit:cover; border-radius:50%; margin-right:8px;vertical-align:middle;">
                    @else
                        <div
                            style="width:36px;height:36px;display:inline-block;border-radius:50%;background:#eee;margin-right:8px;vertical-align:middle;">
                        </div>
                    @endif
                    <strong style="vertical-align:middle;">{{ $product->name }}</strong>
                </td>
                <td class="text-center align-middle">
                    @if ($product->promotion_price && $product->promotion_price < $product->price)
                        <span
                            style="text-decoration:line-through; color:#888;">{{ number_format($product->price, 0, ',', '.') }}{{ config('custom.currency') }}</span><br>
                        <span class="badge bg-danger"
                            style="font-size:1.08em;border-radius:13px;padding:0.35em 1em;">{{ number_format($product->promotion_price, 0, ',', '.') }}{{ config('custom.currency') }}</span>
                    @else
                        <span>{{ number_format($product->price, 0, ',', '.') }}{{ config('custom.currency') }}</span>
                    @endif
                </td>
                <td class="text-center align-middle">{{ __('Đơn giản') }}</td>
                <td class="text-center align-middle">
                    <input type="text" pattern="\\d*" class="form-control text-end" data-role="qty-input"
                        data-product-id="{{ $product->id }}" data-variation-id="" value="{{ (int) $simpleQty }}"
                        @disabled(!$isSuperAdmin) />
                </td>
            </tr>
        @else
            <tr class="table-active">
                <td colspan="4">
                    @if ($product->avatar)
                        <img src="{{ asset($product->avatar) }}" alt="{{ $product->name }}"
                            style="width:36px; height:36px; object-fit:cover; border-radius:50%; margin-right:8px;vertical-align:middle;">
                    @else
                        <div
                            style="width:36px;height:36px;display:inline-block;border-radius:50%;background:#eee;margin-right:8px;vertical-align:middle;">
                        </div>
                    @endif
                    <strong style="vertical-align:middle;">{{ $product->name }}</strong>
                </td>
            </tr>
            @foreach ($product->productVariations as $variationIdx => $variation)
                @php
                    $key = ($product->id ?: 0) . ':' . ($variation->id ?: 0);
                    $qty = optional($inventoryRows->get($key)[0] ?? null)->qty ?? 0;
                    $optionNames = $variation->attributeVariations->pluck('name')->implode(' / ');
                    // Lấy image của biến thể nếu có, không thì của product
                    $img = $variation->image ?: $product->avatar;
                    $price = $variation->promotion_price ?? $variation->price;
                    $rootPrice = $variation->price;
                    $hasPromotion = $variation->promotion_price && $variation->promotion_price < $variation->price;
                    if ($price === null) {
                        $price = $product->promotion_price ?? $product->price;
                        $rootPrice = $product->price;
                        $hasPromotion = $product->promotion_price && $product->promotion_price < $product->price;
                    }
                @endphp
                <tr
                    @if ($variationIdx === 0) style="background-color: #cad8de; border-top: 3px solid #bfc6d1;"
                    @else
                        style="background-color: #cad8de;" @endif>
                    <td>
                        @if ($img)
                            <img src="{{ asset($img) }}" alt="{{ $optionNames ?: $product->name }}"
                                style="width:36px; height:36px; object-fit:cover; border-radius:50%; margin-right:8px;vertical-align:middle;">
                        @else
                            <div
                                style="width:36px;height:36px;display:inline-block;border-radius:50%;background:#eee;margin-right:8px;vertical-align:middle;">
                            </div>
                        @endif
                        <span style="vertical-align:middle;">{{ $optionNames ?: __('Phân loại') }}</span>
                    </td>
                    <td class="text-center align-middle">
                        @if ($hasPromotion)
                            <span
                                style="text-decoration:line-through; color:#888;">{{ number_format($rootPrice, 0, ',', '.') }}{{ config('custom.currency') }}</span><br>
                            <span class="badge bg-danger"
                                style="font-size:1.08em;border-radius:13px;padding:0.35em 1em;">{{ number_format($price, 0, ',', '.') }}{{ config('custom.currency') }}</span>
                        @else
                            <span>{{ number_format($price, 0, ',', '.') }}{{ config('custom.currency') }}</span>
                        @endif
                    </td>
                    <td class="text-center align-middle">{{ __('Phân loại') }}</td>
                    <td class="text-center align-middle">
                        <input type="text" pattern="\\d*" class="form-control text-end" data-role="qty-input"
                            data-product-id="{{ $product->id }}" data-variation-id="{{ $variation->id }}"
                            value="{{ (int) $qty }}" @disabled(!$isSuperAdmin) />
                    </td>
                </tr>
            @endforeach
        @endif
    @endforeach
@endif

@if (!isset($qtyInputScriptRequired))
    @php $qtyInputScriptRequired = true; @endphp
    <script>
        document.addEventListener('input', function(e) {
            if (e.target && e.target.matches('[data-role="qty-input"]')) {
                // Xóa mọi ký tự không phải số, ngăn số âm, bỏ chữ
                let cleaned = e.target.value.replace(/[^\d]/g, '');
                if (cleaned.startsWith('0') && cleaned.length > 1) cleaned = cleaned.replace(/^0+/, '');
                e.target.value = cleaned;
            }
        });
        document.addEventListener('keydown', function(e) {
            // Không cho nhập ký tự -, +, e, E, ., hoặc bất kỳ ký tự không phải số (cho phép backspace, tab, mũi tên)
            if (e.target && e.target.matches('[data-role="qty-input"]')) {
                if (["-", "+", "e", "E", "."].includes(e.key)) {
                    e.preventDefault();
                }
                // Ngăn phím số âm
                if (e.key === 'ArrowDown' && e.target.value === '') {
                    e.preventDefault();
                }
            }
        });
        document.addEventListener('paste', function(e) {
            if (e.target && e.target.matches('[data-role="qty-input"]')) {
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(paste)) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endif
