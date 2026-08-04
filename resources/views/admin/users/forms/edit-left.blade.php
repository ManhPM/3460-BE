<div class="col-12 col-md-9">
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black active" id="tab-login" data-bs-toggle="tab" data-bs-target="#pane-login"
                type="button" role="tab" aria-controls="pane-login"
                aria-selected="true">
                <i class="ti ti-lock me-1"></i>{{ __('Đăng nhập') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black" id="tab-basic" data-bs-toggle="tab" data-bs-target="#pane-basic"
                type="button" role="tab" aria-controls="pane-basic"
                aria-selected="false">
                <i class="ti ti-user me-1"></i>{{ __('Cơ bản') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black" id="tab-membership" data-bs-toggle="tab"
                data-bs-target="#pane-membership" type="button" role="tab" aria-controls="pane-membership"
                aria-selected="false">
                <i class="ti ti-crown me-1"></i>{{ __('Hạng thành viên') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black" id="tab-affiliate" data-bs-toggle="tab" data-bs-target="#pane-affiliate"
                type="button" role="tab" aria-controls="pane-affiliate"
                aria-selected="false">
                <i class="ti ti-share me-1"></i>{{ __('Affiliate') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black" id="tab-point-history" data-bs-toggle="tab"
                data-bs-target="#pane-point-history" type="button" role="tab" aria-controls="pane-point-history"
                aria-selected="false">
                <i class="ti ti-star me-1"></i>{{ __('Lịch sử điểm') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pane-login" role="tabpanel" aria-labelledby="tab-login">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Đăng nhập') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="phone" text="{{ __('Số điện thoại') }}" icon="ti ti-phone-check" />
                            <x-input-phone :value="$instance->phone" name="phone" :placeholder="__('Số điện thoại')" />
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <x-label for="email" text="{{ __('Email đăng nhập') }}" icon="ti ti-mail" />
                            <x-input-email id="emailInput" name="email" :value="$instance->email" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="password" text="{{ __('Mật khẩu') }}" icon="ti ti-key" required="true" />
                            <x-input-password name="password" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="password_confirmation" text="{{ __('Xác nhận mật khẩu') }}" icon="ti ti-key"
                                required="true" />
                            <x-input-password name="password_confirmation" data-parsley-equalto="input[name='password']"
                                data-parsley-equalto-message="{{ __('Mật khẩu không khớp.') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-basic" role="tabpanel" aria-labelledby="tab-basic">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Cơ bản') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <x-label for="fullname" text="{{ __('Họ và tên') }}" icon="ti ti-user-edit"
                                required="true" />
                            <x-input name="fullname" :value="$instance->fullname" :required="true"
                                placeholder="{{ __('Họ và tên') }}" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="birthday" text="{{ __('Ngày sinh') }}" icon="ti ti-calendar" />
                            <x-input class="flatpickr" name="birthday" :value="isset($instance->birthday) ? format_date($instance->birthday) : null" />
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <x-label for="gender" text="{{ __('Giới tính') }}" icon="ti ti-gender-female"
                                required="true" />
                            <x-select name="gender">
                                <x-select-option value="" :title="__('Chọn Giới tính')" />
                                @foreach ($gender as $key => $value)
                                    <x-select-option :option="$instance->gender->value" :value="$key" :title="__($value)" />
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-membership" role="tabpanel" aria-labelledby="tab-membership">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Hạng thành viên hiện tại') }}</h2>
                </div>
                <div class="card-body">
                    @if ($instance->member)
                        <div class="membership-card"
                            style="background: linear-gradient(135deg, {{ $instance->member->color_1 ?? '#6366f1' }}, {{ $instance->member->color_2 ?? '#8b5cf6' }}); border-radius: 15px; padding: 30px; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
                            <div
                                style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;">
                            </div>
                            <div
                                style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;">
                            </div>

                            <div style="position: relative; z-index: 1;">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h3 class="mb-1" style="font-weight: 700; font-size: 28px;">
                                            {{ $instance->member->name }}</h3>
                                        <p class="mb-0" style="opacity: 0.9; font-size: 14px;">
                                            {{ __('Hạng thành viên') }}</p>
                                    </div>
                                    @if ($instance->member->icon)
                                        <div style="font-size: 48px; opacity: 0.9;">
                                            <i class="{{ $instance->member->icon }}"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="row mt-4">
                                    <div class="col-6">
                                        <p class="mb-1" style="opacity: 0.8; font-size: 12px;">
                                            {{ __('Điểm tích lũy') }}</p>
                                        <h4 class="mb-0" style="font-weight: 600;">
                                            {{ number_format($instance->membership_level_points ?? 0) }}</h4>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1" style="opacity: 0.8; font-size: 12px;">{{ __('Giảm giá') }}
                                        </p>
                                        <h4 class="mb-0" style="font-weight: 600;">
                                            {{ $instance->member->discount_percentage ?? 0 }}%</h4>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.3);">
                                    <p class="mb-0" style="opacity: 0.8; font-size: 12px;">
                                        {{ __('Điểm tối thiểu') }}:
                                        <strong>{{ number_format($instance->member->min_points) }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>{{ __('Người dùng chưa có hạng thành viên') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Tất cả hạng thành viên') }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $membershipLevels = \App\Models\MembershipLevel::orderBy('min_points', 'asc')->get();
                        @endphp
                        @foreach ($membershipLevels as $level)
                            <div class="col-md-6 mb-3">
                                <div class="membership-card"
                                    style="background: linear-gradient(135deg, {{ $level->color_1 ?? '#6366f1' }}, {{ $level->color_2 ?? '#8b5cf6' }}); border-radius: 12px; padding: 20px; color: white; box-shadow: 0 5px 15px rgba(0,0,0,0.15); position: relative; overflow: hidden; {{ $instance->membership_id == $level->id ? 'border: 3px solid #fbbf24;' : '' }}">
                                    <div
                                        style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;">
                                    </div>

                                    <div style="position: relative; z-index: 1;">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="mb-1" style="font-weight: 700; font-size: 20px;">
                                                    {{ $level->name }}</h5>
                                                @if ($instance->membership_id == $level->id)
                                                    <span class="badge bg-warning text-dark"
                                                        style="font-size: 10px;">{{ __('Hạng hiện tại') }}</span>
                                                @endif
                                            </div>
                                            @if ($level->icon)
                                                <div style="font-size: 32px; opacity: 0.9;">
                                                    <i class="{{ $level->icon }}"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-0" style="opacity: 0.8; font-size: 11px;">
                                                    {{ __('Điểm tối thiểu') }}</p>
                                                <h6 class="mb-0" style="font-weight: 600;">
                                                    {{ number_format($level->min_points) }}</h6>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-0" style="opacity: 0.8; font-size: 11px;">
                                                    {{ __('Giảm giá') }}</p>
                                                <h6 class="mb-0" style="font-weight: 600;">
                                                    {{ $level->discount_percentage }}%</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-affiliate" role="tabpanel" aria-labelledby="tab-affiliate">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Affiliate') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="mb-3">
                        <x-label for="bank_name" text="{{ __('Tên ngân hàng nhận hoa hồng') }}"
                            icon="ti ti-building-bank" required="true" />
                        <x-input name="bank_name" :value="$instance->bank_name" :placeholder="__('Tên ngân hàng nhận hoa hồng')" />
                    </div>
                    <div class="mb-3">
                        <x-label for="bank_account" text="{{ __('Tên chủ tài khoản nhận hoa hồng') }}"
                            icon="ti ti-user-dollar" required="true" />
                        <x-input name="bank_account" :value="$instance->bank_account" :placeholder="__('Tên chủ tài khoản nhận hoa hồng')" />
                    </div>
                    <div class="mb-3">
                        <x-label for="bank_account_number" text="{{ __('Số tài khoản nhận hoa hồng') }}"
                            icon="ti ti-credit-card-refund" required="true" />
                        <x-input name="bank_account_number" :value="$instance->bank_account_number" :placeholder="__('Số tài khoản nhận hoa hồng')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="affiliate_code" text="{{ __('Mã giới thiệu') }}" icon="ti ti-tag" />
                        <x-input disabled :value="$instance->affiliate_code" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="wallet_balance" text="{{ __('Số dư') }}" icon="ti ti-currency-dollar" />
                        <x-input disabled :value="$instance->wallet_balance" />
                    </div>
                </div>
            </div>

            @php
                $referredUsers = \App\Models\User::whereNotNull('referrer_code')
                    ->where(function($q) use ($instance) {
                        if ($instance->affiliate_code) {
                            $q->where('referrer_code', $instance->affiliate_code);
                        }
                        if ($instance->code) {
                            $q->orWhere('referrer_code', $instance->code);
                        }
                    })
                    ->with('member')
                    ->latest()
                    ->get();
            @endphp

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ __('Danh sách người được giới thiệu') }} ({{ $referredUsers->count() }})</h2>
                </div>
                <div class="card-body p-0">
                    @if($referredUsers->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">#</th>
                                        <th>{{ __('Họ và tên') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Số điện thoại') }}</th>
                                        <th>{{ __('Hạng thành viên') }}</th>
                                        <th>{{ __('Ngày đăng ký') }}</th>
                                        <th class="text-center" style="width: 100px;">{{ __('Thao tác') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referredUsers as $key => $referredUser)
                                        <tr>
                                            <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset($referredUser->avatar ?? 'public/assets/images/default-avatar.png') }}"
                                                         alt="Avatar"
                                                         class="rounded-circle me-2"
                                                         style="width: 36px; height: 36px; object-fit: cover;">
                                                    <div>
                                                        <a href="{{ route('admin.user.edit', $referredUser->id) }}" class="fw-semibold text-primary text-decoration-none">
                                                            {{ $referredUser->fullname }}
                                                        </a>
                                                        @if($referredUser->code)
                                                            <div class="text-muted small">Mã: {{ $referredUser->code }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $referredUser->email ?? '-' }}</td>
                                            <td>{{ $referredUser->phone ?? '-' }}</td>
                                            <td>
                                                @if($referredUser->member)
                                                    <span class="badge bg-label-info">{{ $referredUser->member->name }}</span>
                                                @else
                                                    <span class="badge bg-label-secondary">{{ __('Mặc định') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $referredUser->created_at ? $referredUser->created_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.user.edit', $referredUser->id) }}"
                                                   class="btn btn-sm btn-icon btn-outline-primary"
                                                   title="{{ __('Chi tiết') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ti ti-users-minus mb-2" style="font-size: 36px; opacity: 0.5;"></i>
                            <p class="mb-0">{{ __('Chưa có người dùng nào đăng ký qua mã giới thiệu này.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-point-history" role="tabpanel" aria-labelledby="tab-point-history">
            <!-- Card hiển thị số điểm đang có -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="membership-card"
                        style="background: linear-gradient(135deg, #f59e0b, #f97316); border-radius: 15px; padding: 30px; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
                        <div
                            style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;">
                        </div>
                        <div
                            style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;">
                        </div>

                        <div style="position: relative; z-index: 1;">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h3 class="mb-1" style="font-weight: 700; font-size: 28px;">
                                        {{ __('Điểm thưởng') }}</h3>
                                    <p class="mb-0" style="opacity: 0.9; font-size: 14px;">
                                        {{ __('Số điểm hiện có') }}</p>
                                </div>
                                <div style="font-size: 48px; opacity: 0.9;">
                                    <i class="ti ti-star"></i>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-6">
                                    <p class="mb-1" style="opacity: 0.8; font-size: 12px;">
                                        {{ __('Điểm có thể dùng') }}</p>
                                    <h4 class="mb-0" style="font-weight: 600;">
                                        {{ number_format($instance->points ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-dark" id="tab-point-earned" data-bs-toggle="pill"
                        data-bs-target="#pane-point-earned" type="button" role="tab"
                        aria-controls="pane-point-earned" aria-selected="true">
                        <i class="ti ti-sparkles me-1"></i>{{ __('Lịch sử tích điểm') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark" id="tab-point-used" data-bs-toggle="pill"
                        data-bs-target="#pane-point-used" type="button" role="tab"
                        aria-controls="pane-point-used" aria-selected="false">
                        <i class="ti ti-history me-1"></i>{{ __('Lịch sử dùng điểm') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Lịch sử tích điểm -->
                <div class="tab-pane fade show active" id="pane-point-earned" role="tabpanel"
                    aria-labelledby="tab-point-earned">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="mb-0">{{ __('Lịch sử tích điểm') }}</h2>
                        </div>
                        <div class="card-body">
                            <!-- Filter -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Tìm kiếm mã đơn hàng') }}</label>
                                    <input type="text" class="form-control" id="pointEarnedSearch"
                                        placeholder="{{ __('Nhập mã đơn hàng') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Từ ngày') }}</label>
                                    <input type="date" class="form-control" id="pointEarnedDateFrom">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Đến ngày') }}</label>
                                    <input type="date" class="form-control" id="pointEarnedDateTo">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" id="pointEarnedFilterBtn">
                                        <i class="ti ti-filter me-1"></i>{{ __('Lọc') }}
                                    </button>
                                </div>
                            </div>
                            <!-- Content -->
                            <div id="pointEarnedContent">
                                <div class="text-center py-4">
                                    <span class="spinner-border spinner-border-sm me-2"></span>{{ __('Đang tải...') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lịch sử dùng điểm -->
                <div class="tab-pane fade" id="pane-point-used" role="tabpanel" aria-labelledby="tab-point-used">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="mb-0">{{ __('Lịch sử dùng điểm') }}</h2>
                        </div>
                        <div class="card-body">
                            <!-- Filter -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Tìm kiếm mã đơn hàng') }}</label>
                                    <input type="text" class="form-control" id="pointUsedSearch"
                                        placeholder="{{ __('Nhập mã đơn hàng') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Từ ngày') }}</label>
                                    <input type="date" class="form-control" id="pointUsedDateFrom">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Đến ngày') }}</label>
                                    <input type="date" class="form-control" id="pointUsedDateTo">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" id="pointUsedFilterBtn">
                                        <i class="ti ti-filter me-1"></i>{{ __('Lọc') }}
                                    </button>
                                </div>
                            </div>
                            <!-- Content -->
                            <div id="pointUsedContent">
                                <div class="text-center py-4">
                                    <span class="spinner-border spinner-border-sm me-2"></span>{{ __('Đang tải...') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure correct tab shown on anchor/hash navigation (optional enhancement)
    if (window.location.hash) {
        var triggerEl = document.querySelector('button[data-bs-target="' + window.location.hash + '"]');
        if (triggerEl) new bootstrap.Tab(triggerEl).show();
    }

    // Point History AJAX
    (function() {
        const userId = {{ $instance->id }};
        let pointEarnedPage = 1;
        let pointUsedPage = 1;

        // Point Earned History
        function fetchPointEarnedHistory() {
            const search = document.getElementById('pointEarnedSearch').value || '';
            const dateFrom = document.getElementById('pointEarnedDateFrom').value || '';
            const dateTo = document.getElementById('pointEarnedDateTo').value || '';
            const url =
                `{{ route('admin.user.point-earned-history', ['userId' => $instance->id]) }}?page=${pointEarnedPage}&search=${encodeURIComponent(search)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

            document.getElementById('pointEarnedContent').innerHTML =
                '<div class="text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span>{{ __('Đang tải...') }}</div>';

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    document.getElementById('pointEarnedContent').innerHTML = res.html;
                    bindPointEarnedPagination();
                },
                error: function() {
                    document.getElementById('pointEarnedContent').innerHTML =
                        '<div class="alert alert-danger text-center">{{ __('Lỗi tải dữ liệu') }}</div>';
                }
            });
        }

        function bindPointEarnedPagination() {
            const pagination = document.getElementById('pointEarnedPagination');
            if (pagination) {
                pagination.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        pointEarnedPage = parseInt(this.getAttribute('data-page'));
                        fetchPointEarnedHistory();
                    });
                });
            }
        }

        // Point Used History
        function fetchPointUsedHistory() {
            const search = document.getElementById('pointUsedSearch').value || '';
            const dateFrom = document.getElementById('pointUsedDateFrom').value || '';
            const dateTo = document.getElementById('pointUsedDateTo').value || '';
            const url =
                `{{ route('admin.user.point-used-history', ['userId' => $instance->id]) }}?page=${pointUsedPage}&search=${encodeURIComponent(search)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

            document.getElementById('pointUsedContent').innerHTML =
                '<div class="text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span>{{ __('Đang tải...') }}</div>';

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    document.getElementById('pointUsedContent').innerHTML = res.html;
                    bindPointUsedPagination();
                },
                error: function() {
                    document.getElementById('pointUsedContent').innerHTML =
                        '<div class="alert alert-danger text-center">{{ __('Lỗi tải dữ liệu') }}</div>';
                }
            });
        }

        function bindPointUsedPagination() {
            const pagination = document.getElementById('pointUsedPagination');
            if (pagination) {
                pagination.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        pointUsedPage = parseInt(this.getAttribute('data-page'));
                        fetchPointUsedHistory();
                    });
                });
            }
        }

        // Event listeners
        document.getElementById('pointEarnedFilterBtn').addEventListener('click', function() {
            pointEarnedPage = 1;
            fetchPointEarnedHistory();
        });

        document.getElementById('pointUsedFilterBtn').addEventListener('click', function() {
            pointUsedPage = 1;
            fetchPointUsedHistory();
        });

        // Load data when tab is shown
        const pointHistoryTab = document.getElementById('tab-point-history');
        if (pointHistoryTab) {
            pointHistoryTab.addEventListener('shown.bs.tab', function() {
                fetchPointEarnedHistory();
            });
        }

        const pointEarnedTab = document.getElementById('tab-point-earned');
        if (pointEarnedTab) {
            pointEarnedTab.addEventListener('shown.bs.tab', function() {
                fetchPointEarnedHistory();
            });
        }

        const pointUsedTab = document.getElementById('tab-point-used');
        if (pointUsedTab) {
            pointUsedTab.addEventListener('shown.bs.tab', function() {
                fetchPointUsedHistory();
            });
        }

        // Enter key to search
        document.getElementById('pointEarnedSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                pointEarnedPage = 1;
                fetchPointEarnedHistory();
            }
        });

        document.getElementById('pointUsedSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                pointUsedPage = 1;
                fetchPointUsedHistory();
            }
        });
    })();
</script>
