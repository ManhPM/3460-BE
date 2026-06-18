@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h2 class="mb-0">{{ __('Quản lý tồn chi nhánh') }}</h2>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($isSuperAdmin && auth('admin')->id() != 1)
                                <label for="admin_id" class="mb-0 text-nowrap">{{ __('Chi nhánh') }}</label>
                                <select id="admin_id" class="form-select" style="min-width: 250px;">
                                    <option value="">-- {{ __('Chọn chi nhánh') }} --</option>
                                    @foreach ($admins as $a)
                                        <option value="{{ $a->id }}" @selected($selectedAdminId == $a->id)>
                                            {{ $a->branch_name . ' - ' . $a->branch_phone . ' - ' . $a->branch_address }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{-- SuperAdmin tổng (id=1) hoặc admin branch: dùng hidden input, auto chọn --}}
                                <input type="hidden" id="admin_id" value="{{ (int) $selectedAdminId }}" />
                            @endif


                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input id="search" type="text" class="form-control"
                                placeholder="{{ __('Tìm kiếm theo tên sản phẩm') }}" />
                        </div>
                    </div>
                    <div class="table-responsive position-relative">
                        <table class="table table-bordered align-middle" id="inventoryTable" style="min-width: 900px;">
                            <thead>
                                <tr>
                                    <th style="width:38%">{{ __('Sản phẩm/Phân loại') }}</th>
                                    <th style="width:24%" class="text-center">{{ __('Giá/Khuyến mãi') }}</th>
                                    <th style="width:18%" class="text-center">{{ __('Loại') }}</th>
                                    <th style="width:20%" class="text-center">{{ __('Tồn chi nhánh') }}</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTbody">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        {{ __('Đang tải...') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="loadMoreContainer" class="text-center mt-3" style="display:none">
                        <button id="btnLoadMore" class="btn btn-primary">{{ __('Xem thêm') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('libs-js')
@endpush

@push('custom-js')
    <script>
        (function() {
            const $admin = document.getElementById('admin_id');
            const $search = document.getElementById('search');
            const $tbody = document.getElementById('inventoryTbody');
            const $loadMoreContainer = document.getElementById('loadMoreContainer');
            const $btnLoadMore = document.getElementById('btnLoadMore');

            let typingTimer;
            let currentPage = 1;
            let isLoading = false;

            const debounce = (fn, delay = 400) => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(fn, delay);
            };

            function fetchData(page = 1) {
                const adminId = ($admin && $admin.value) ? $admin.value : document.getElementById('admin_id').value;
                if (!adminId) {
                    $tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center text-muted py-5">{{ __('Chưa chọn chi nhánh') }}</td></tr>';
                    $loadMoreContainer.style.display = 'none';
                    return;
                }

                if (isLoading) return;
                isLoading = true;

                if (page === 1) {
                    $tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center text-muted py-5">{{ __('Đang tải...') }}</td></tr>';
                    $loadMoreContainer.style.display = 'none';
                } else {
                    $btnLoadMore.disabled = true;
                    $btnLoadMore.innerText = '{{ __('Đang tải...') }}';
                }

                const q = $search.value || '';
                const url =
                    `{{ route('admin.inventory.data') }}?admin_id=${encodeURIComponent(adminId)}&q=${encodeURIComponent(q)}&page=${page}`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(res) {
                        if (page === 1) {
                            $tbody.innerHTML = res.html;
                        } else {
                            $tbody.insertAdjacentHTML('beforeend', res.html);
                        }

                        if (res.has_more) {
                            $loadMoreContainer.style.display = 'block';
                            $btnLoadMore.disabled = false;
                            $btnLoadMore.innerText = '{{ __('Xem thêm') }}';
                            currentPage = res.next_page;
                        } else {
                            $loadMoreContainer.style.display = 'none';
                        }
                        bindQtyInputs();
                    },
                    error: function() {
                        if (page === 1) {
                            $tbody.innerHTML =
                                '<tr><td colspan="4" class="text-center text-danger py-5">{{ __('Lỗi tải dữ liệu') }}</td></tr>';
                        } else {
                            showToastify('error', 'Lỗi', 'Không thể tải thêm dữ liệu');
                            $btnLoadMore.disabled = false;
                            $btnLoadMore.innerText = '{{ __('Xem thêm') }}';
                        }
                    },
                    complete: function() {
                        isLoading = false;
                    }
                });
            }

            function bindQtyInputs() {
                document.querySelectorAll('[data-role="qty-input"]').forEach((input) => {
                    if (input.classList.contains('js-bound')) return;
                    input.addEventListener('input', () => debounce(() => saveQty(input)));
                    input.addEventListener('change', () => saveQty(input));
                    input.classList.add('js-bound');
                });
            }

            function isValidQty(val) {
                return /^\d+$/.test(val) && Number.isInteger(Number(val)) && Number(val) >= 0;
            }

            function saveQty(input) {
                const qtyVal = input.value;
                if (!isValidQty(qtyVal)) {
                    input.classList.add('is-invalid');
                    setTimeout(() => input.classList.remove('is-invalid'), 1000);
                    showToastify('error', 'Lỗi', 'Số lượng không hợp lệ');
                    return;
                }
                const payload = {
                    admin_id: ($admin && $admin.value) ? $admin.value : document.getElementById('admin_id').value,
                    product_id: input.getAttribute('data-product-id') || null,
                    product_variation_id: input.getAttribute('data-variation-id') || null,
                    qty: input.value || 0
                };
                $.ajax({
                    url: `{{ route('admin.inventory.update_qty') }}`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function() {
                        input.classList.add('is-valid');
                        setTimeout(() => input.classList.remove('is-valid'), 600);
                    },
                    error: function() {
                        input.classList.add('is-invalid');
                        setTimeout(() => input.classList.remove('is-invalid'), 1000);
                    }
                });
            }



            // ── Event listeners ban đầu ───────────────────────────────────────────

            if ($admin) {
                $admin.addEventListener('change', () => fetchData(1));
            }
            if ($search) {
                $search.addEventListener('input', () => debounce(() => fetchData(1)));
            }
            if ($btnLoadMore) {
                $btnLoadMore.addEventListener('click', () => fetchData(currentPage));
            }

            // Auto load if admin_id preset
            if (document.getElementById('admin_id').value) {
                fetchData(1);
            }
        })();
    </script>
@endpush
