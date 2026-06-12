@extends('admin.layouts.master')
@include('admin.dashboard.style')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            @include('admin.dashboard.partials.date-filter')

            {{-- 
            <!-- Tab chi nhánh cho SuperAdmin -->
            @if ($isSuperAdmin && $branches->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Chi nhánh</h5>
                            </div>
                            <div class="card-body">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.dashboard', ['admin_id' => '']) }}"
                                        class="btn {{ !$adminId ? 'btn-primary' : 'btn-outline-primary' }}">
                                        Tất cả chi nhánh
                                    </a>
                                    @foreach ($branches as $branch)
                                        <a href="{{ route('admin.dashboard', ['admin_id' => $branch->id]) }}"
                                            class="btn {{ $adminId == $branch->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                            {{ $branch->branch_name ?? $branch->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            --}}

            <!-- Thống kê tổng quan -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3 class="mb-3">📊 Tổng quan</h3>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-blue text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($totalOrders) }}</h4>
                                    <p class="card-text metric-label">Tổng đơn hàng</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-receipt metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-yellow text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($pendingOrders) }}</h4>
                                    <p class="card-text metric-label">Đơn chờ</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-receipt-off metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-green text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($completedOrders) }}</h4>
                                    <p class="card-text metric-label">Đơn hoàn thành</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-receipt-2 metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-teal text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($totalRevenue) }}
                                        {{ config('custom.currency') }}</h4>
                                    <p class="card-text metric-label">Tổng doanh thu</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-coin metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê mở rộng -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card metric-card gradient-purple text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($newCustomers) }}</h4>
                                    <p class="card-text metric-label">KH mới (tháng)</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-user-plus metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-indigo text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($newCustomersThisYear) }}</h4>
                                    <p class="card-text metric-label">KH mới (năm)</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-user-up metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-pink text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($totalProductsSold) }}</h4>
                                    <p class="card-text metric-label">SP đã bán</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-package metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-teal text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ format_price($averageOrderValue) }}</h4>
                                    <p class="card-text metric-label">Giá trị TB/đơn</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-cash metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm & Đánh giá -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card metric-card gradient-blue text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($totalProducts) }}</h4>
                                    <p class="card-text metric-label">Tổng sản phẩm</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-brand-producthunt metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-green text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($activeProducts) }}</h4>
                                    <p class="card-text metric-label">Sản phẩm đang bán</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-check metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-purple text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($inStockProducts) }}</h4>
                                    <p class="card-text metric-label">Còn hàng</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-box metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card gradient-red text-white position-relative">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title metric-number">{{ number_format($outOfStockProducts) }}</h4>
                                    <p class="card-text metric-label">Hết hàng</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-box-off metric-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Biểu đồ doanh thu theo tháng</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tỷ lệ đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="orderPieChart" style="height: 300px;"></canvas>
                            <div class="mt-3">
                                <ul class="list-unstyled small">
                                    @foreach ($orderStatusLabels as $idx => $label)
                                        <li class="d-flex justify-content-between">
                                            <span>{{ $label }}</span>
                                            <span>{{ number_format($orderStatusCounts[$idx] ?? 0) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="{{ asset('libs/chart/chart.min.js') }}"></script>
    <script>
        // Revenue monthly line
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Doanh thu',
                    data: {!! json_encode($monthlyRevenue) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Order status doughnut
        const orderPieCtx = document.getElementById('orderPieChart').getContext('2d');

        // Tạo gradient cho từng màu
        const gradientPending = orderPieCtx.createLinearGradient(0, 0, 200, 200);
        gradientPending.addColorStop(0, '#f7971e');
        gradientPending.addColorStop(1, '#ffd200');

        const gradientConfirmed = orderPieCtx.createLinearGradient(0, 0, 200, 200);
        gradientConfirmed.addColorStop(0, '#667eea');
        gradientConfirmed.addColorStop(1, '#764ba2');

        const gradientDelivering = orderPieCtx.createLinearGradient(0, 0, 200, 200);
        gradientDelivering.addColorStop(0, '#ff5f6d');
        gradientDelivering.addColorStop(1, '#ffc371');

        const gradientCompleted = orderPieCtx.createLinearGradient(0, 0, 200, 200);
        gradientCompleted.addColorStop(0, '#43e97b');
        gradientCompleted.addColorStop(1, '#38f9d7');

        const gradientCancelled = orderPieCtx.createLinearGradient(0, 0, 200, 200);
        gradientCancelled.addColorStop(0, '#f857a6');
        gradientCancelled.addColorStop(1, '#ff5858');

        // Tạo chart
        new Chart(orderPieCtx, {
            type: 'doughnut',
            data: {
                labels: @json($orderStatusLabels),
                datasets: [{
                    data: @json($orderStatusCounts),
                    backgroundColor: [
                        gradientPending,
                        gradientConfirmed,
                        gradientDelivering,
                        gradientCompleted,
                        gradientCancelled
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

    <!-- Popup Warning cho đơn hàng chưa xử lý -->
    @if ($pendingOrdersList && $pendingOrdersList->count() > 0)
        <div class="modal fade show" id="pendingOrdersModal" tabindex="-1" role="dialog"
            style="display: block; z-index: 9999;" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Cảnh báo: Có {{ $pendingOrdersList->count() }} đơn hàng chưa được xử lý
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning mb-3">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Các đơn hàng này đang ở trạng thái "Chờ xác nhận" và cần được xử lý
                            ngay.
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingOrdersList as $order)
                                        <tr>
                                            <td><strong>{{ $order->code }}</strong></td>
                                            <td>{{ $order->fullname ?? 'N/A' }}</td>
                                            <td>{{ number_format($order->total, 0, ',', '.') }}
                                                {{ config('custom.currency') }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.order.edit', $order->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ti ti-edit me-1"></i>Xử lý
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" id="pendingOrdersBackdrop"></div>

        <script>
            // Tự động hiển thị popup khi có đơn hàng chưa xử lý
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('pendingOrdersModal'));
                modal.show();

                // Đóng backdrop khi đóng modal
                document.getElementById('pendingOrdersModal').addEventListener('hidden.bs.modal', function() {
                    const backdrop = document.getElementById('pendingOrdersBackdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                });
            });
        </script>
    @endif
@endsection
