<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin đơn hàng') . ' #' . $order->code }}</h2>
        </div>
        <div class="row card-body">
            <x-input type="hidden" name="order[admin_id]" :value="$order->admin_id" />
            {{-- Ẩn phần chi nhánh
            <div class="mb-3">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-building-store me-2"></i>{{ __('Chi nhánh') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-primary text-white rounded">
                                            <i class="ti ti-building"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1 fw-semibold">{{ $order->admin->branch_name ?? '' }}</p>
                                        <p class="mb-0 text-muted small">
                                            <i class="ti ti-phone me-1"></i>{{ $order->admin->branch_phone ?? '' }}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="ti ti-map-pin me-1"></i>{{ $order->admin->branch_address ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            --}}

            <div class="col-12 mb-3">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-user me-2"></i>{{ __('Thông tin khách hàng') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-primary text-white rounded">
                                            <i class="ti ti-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Họ và tên') }}</label>
                                        <p class="mb-0 fw-semibold">{{ $order->fullname }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-info text-white rounded">
                                            <i class="ti ti-mail"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Email') }}</label>
                                        <p class="mb-0 fw-semibold">{{ $order->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-success text-white rounded">
                                            <i class="ti ti-phone"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label
                                            class="form-label text-muted small mb-1">{{ __('Số điện thoại') }}</label>
                                        <p class="mb-0 fw-semibold">{{ $order->phone }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-warning text-white rounded">
                                            <i class="ti ti-map-pin"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Địa chỉ') }}</label>
                                        <p class="mb-0 fw-semibold">{{ $order->address }}</p>
                                    </div>
                                </div>
                            </div>
                            @php
                                $orderUser = $order->user;
                                $referrer = null;
                                if ($orderUser && !empty($orderUser->referrer_code)) {
                                    $referrer = \App\Models\User::where('affiliate_code', $orderUser->referrer_code)
                                        ->orWhere('code', $orderUser->referrer_code)
                                        ->first();
                                }
                            @endphp
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm text-white rounded" style="background-color: #6f42c1 !important;">
                                            <i class="ti ti-share"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Người giới thiệu (Affiliate)') }}</label>
                                        @if($referrer)
                                            <p class="mb-0">
                                                <a href="{{ route('admin.user.edit', $referrer->id) }}" class="fw-semibold text-primary text-decoration-none" title="{{ __('Xem chi tiết người giới thiệu') }}">
                                                    {{ $referrer->fullname }}
                                                </a>
                                                <span class="badge bg-label-success ms-1">{{ $referrer->affiliate_code ?? $referrer->code }}</span>
                                            </p>
                                        @elseif(!empty($orderUser->referrer_code))
                                            <p class="mb-0 fw-semibold text-dark">
                                                <span class="badge bg-label-warning">{{ $orderUser->referrer_code }}</span>
                                            </p>
                                        @else
                                            <p class="mb-0 text-muted fst-italic">{{ __('Không có (Trực tiếp)') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-map me-2"></i>{{ __('Địa chỉ giao hàng') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-primary text-white rounded">
                                            <i class="ti ti-map"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label
                                            class="form-label text-muted small mb-1">{{ __('Tỉnh/Thành phố') }}</label>
                                        <p class="mb-0 fw-semibold">{{ $order->province->name ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-success text-white rounded">
                                            <i class="ti ti-map-pin"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label
                                            class="form-label text-muted small mb-1">{{ __('Thành phố/Khu vực') }}</label>
                                        <p class="mb-0 fw-semibold">{{ $order->ward->name ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-brand-mastercard me-2"></i>{{ __('Phương thức thanh toán') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-primary text-white rounded">
                                            <i class="ti ti-credit-card"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0 fw-semibold">
                                            {{ App\Enums\Payment\PaymentMethod::getDescription($order->payment_method->value) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-calendar me-2"></i>{{ __('Thông tin thời gian') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-primary text-white rounded">
                                            <i class="ti ti-calendar-plus"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Ngày tạo') }}</label>
                                        <p class="mb-0 fw-semibold">{{ format_datetime($order->created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-info text-white rounded">
                                            <i class="ti ti-truck-delivery"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label
                                            class="form-label text-muted small mb-1">{{ __('Ngày vận chuyển') }}</label>
                                        <p class="mb-0 fw-semibold">
                                            {{ $order->shipping_date ? format_datetime($order->shipping_date) : __('Chưa vận chuyển') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 col-12">
                <x-label text="{{ __('Ghi chú') }}" icon="ti ti-note" />
                <x-textarea name="order[note]">{{ $order->note }}</x-textarea>
            </div>
            @if ($order->payment_method == App\Enums\Payment\PaymentMethod::Banking)
                <div class="mb-3 col-12">
                    @if ($order->payment_image)
                        <x-label class="mb-2" text="{{ __('Hình ảnh thanh toán') }}" icon="ti ti-photo"
                            required="true" />
                        <img class="img-fluid mb-1" src="{{ asset($order->payment_image) }}" alt="Preview">
                    @else
                        <div class="alert alert-warning mt-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Lưu ý:</strong>
                                <span class="ms-2">Khách hàng chưa tải lên hình ảnh chuyển khoản.</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            <div class="col-12 mt-3">
                @include('admin.orders.partials.products', ['order' => $order])
            </div>
        </div>
    </div>
</div>
