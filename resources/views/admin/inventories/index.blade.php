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

                            {{-- Nút import Excel --}}
                            <button type="button" class="btn btn-success d-flex align-items-center gap-1"
                                data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M12 17v-6" />
                                    <path d="M9.5 14.5l2.5 2.5l2.5 -2.5" />
                                </svg>
                                {{ __('Cập nhật từ Excel') }}
                            </button>
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

    {{-- ══════════════════════════════════════════════════════════════════════
         Modal Import Excel
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal modal-blur fade" id="modalImportExcel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="me-2 text-success"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M12 17v-6" />
                            <path d="M9.5 14.5l2.5 2.5l2.5 -2.5" />
                        </svg>
                        {{ __('Cập nhật tồn kho từ Excel') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Hướng dẫn định dạng --}}
                    <div class="alert alert-info mb-3 py-2 small">
                        <strong>Định dạng file Excel (.xlsx/.xls):</strong><br>
                        Cột <strong>E (col 5)</strong>: Tên hàng &nbsp;|&nbsp;
                        Cột <strong>F (col 6)</strong>: Tồn kho &nbsp;|&nbsp;
                        Cột <strong>G (col 7)</strong>: ĐVT (Gói/Lon/Hộp...)<br>
                        <span class="text-muted">Dòng đầu tiên là header, bỏ qua tự động.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Chọn file Excel') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" id="importExcelFile" class="form-control"
                            accept=".xlsx,.xls" />
                        <div class="form-text">Chỉ chấp nhận file .xlsx, .xls — tối đa 10 MB</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Chế độ cập nhật') }}</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="importMode"
                                    id="modeSet" value="set" checked>
                                <label class="form-check-label" for="modeSet">
                                    <strong>Ghi đè</strong>
                                    <span class="text-muted small d-block">Đặt lại số lượng tồn theo file</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="importMode"
                                    id="modeAdd" value="add">
                                <label class="form-check-label" for="modeAdd">
                                    <strong>Cộng dồn</strong>
                                    <span class="text-muted small d-block">Cộng thêm vào tồn hiện tại</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Kết quả sau import --}}
                    <div id="importResult" style="display:none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Đóng') }}
                    </button>
                    <button type="button" id="btnDoImport" class="btn btn-success">
                        <span id="importBtnSpinner" class="spinner-border spinner-border-sm me-1" role="status"
                            style="display:none"></span>
                        {{ __('Cập nhật tồn kho') }}
                    </button>
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

            // ── Import Excel ──────────────────────────────────────────────────────

            const $btnDoImport   = document.getElementById('btnDoImport');
            const $importFile    = document.getElementById('importExcelFile');
            const $importResult  = document.getElementById('importResult');
            const $importSpinner = document.getElementById('importBtnSpinner');

            // Reset modal mỗi khi mở
            document.getElementById('modalImportExcel').addEventListener('show.bs.modal', function() {
                $importFile.value = '';
                $importResult.style.display = 'none';
                $importResult.innerHTML = '';
                document.getElementById('modeSet').checked = true;
            });

            $btnDoImport.addEventListener('click', function() {
                const file = $importFile.files[0];
                if (!file) {
                    showToastify('error', 'Lỗi', 'Vui lòng chọn file Excel.');
                    return;
                }

                const adminId = document.getElementById('admin_id').value;
                if (!adminId) {
                    showToastify('error', 'Lỗi', 'Vui lòng chọn chi nhánh trước.');
                    return;
                }

                const mode = document.querySelector('input[name="importMode"]:checked').value;

                const formData = new FormData();
                formData.append('file', file);
                formData.append('admin_id', adminId);
                formData.append('mode', mode);
                formData.append('_token', '{{ csrf_token() }}');

                // Loading state
                $btnDoImport.disabled = true;
                $importSpinner.style.display = 'inline-block';
                $importResult.style.display = 'none';
                $importResult.innerHTML = '';

                $.ajax({
                    url: '{{ route('admin.inventory.import_excel') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        let notFoundHtml = '';
                        if (res.not_found > 0 && res.not_found_list && res.not_found_list.length > 0) {
                            const items = res.not_found_list
                                .map(item => `<li class="small">${item}</li>`)
                                .join('');
                            notFoundHtml = `
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-link p-0 text-warning" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#notFoundList">
                                        Xem danh sách không tìm thấy (${res.not_found})
                                    </button>
                                    <div class="collapse mt-1" id="notFoundList">
                                        <ul class="mb-0 text-muted" style="max-height:200px;overflow-y:auto">${items}</ul>
                                    </div>
                                </div>`;
                        }

                        $importResult.innerHTML = `
                            <div class="alert alert-success mb-0">
                                <div class="fw-semibold mb-1">✅ ${res.message}</div>
                                <div class="d-flex gap-3 flex-wrap small">
                                    <span>✏️ Cập nhật: <strong>${res.updated}</strong></span>
                                    <span>➕ Tạo mới: <strong>${res.created}</strong></span>
                                    <span>⏭️ Bỏ qua: <strong>${res.skipped}</strong></span>
                                    <span class="${res.not_found > 0 ? 'text-warning fw-semibold' : ''}">
                                        ❌ Không tìm thấy: <strong>${res.not_found}</strong>
                                    </span>
                                </div>
                                ${notFoundHtml}
                            </div>`;
                        $importResult.style.display = 'block';

                        // Reload bảng dữ liệu
                        fetchData(1);
                        showToastify('success', 'Thành công', res.message);
                    },
                    error: function(xhr) {
                        let msg = 'Có lỗi xảy ra khi xử lý file.';
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.message) msg = json.message;
                            if (json.errors) {
                                msg = Object.values(json.errors).flat().join('<br>');
                            }
                        } catch (_) {}
                        $importResult.innerHTML = `<div class="alert alert-danger mb-0">${msg}</div>`;
                        $importResult.style.display = 'block';
                        showToastify('error', 'Lỗi', 'Không thể cập nhật tồn kho.');
                    },
                    complete: function() {
                        $btnDoImport.disabled = false;
                        $importSpinner.style.display = 'none';
                    }
                });
            });

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
