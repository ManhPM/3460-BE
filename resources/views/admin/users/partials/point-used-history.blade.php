@if($orders->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Mã đơn hàng') }}</th>
                    <th>{{ __('Điểm đã dùng') }}</th>
                    <th>{{ __('Tổng tiền') }}</th>
                    <th>{{ __('Ngày sử dụng') }}</th>
                    <th>{{ __('Trạng thái') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.order.edit', $order->id) }}" target="_blank">
                                {{ $order->code }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-danger">
                                -{{ number_format($order->points) }} {{ __('điểm') }}
                            </span>
                        </td>
                        <td>{{ format_price($order->total) }}</td>
                        <td>{{ format_datetime($order->created_at) }}</td>
                        <td>
                            @if($order->status)
                                <span @class(['badge', $order->status->badge()])>
                                    {{ $order->status->description() }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                {{ __('Hiển thị') }} {{ $orders->firstItem() }} - {{ $orders->lastItem() }}
                {{ __('trong tổng số') }} {{ $orders->total() }} {{ __('kết quả') }}
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="pointUsedPagination">
                    @if($orders->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">{{ __('Trước') }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $orders->currentPage() - 1 }}">{{ __('Trước') }}</a>
                        </li>
                    @endif

                    @for($i = 1; $i <= $orders->lastPage(); $i++)
                        @if($i == $orders->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                        @elseif($i == 1 || $i == $orders->lastPage() || ($i >= $orders->currentPage() - 2 && $i <= $orders->currentPage() + 2))
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="{{ $i }}">{{ $i }}</a>
                            </li>
                        @elseif($i == $orders->currentPage() - 3 || $i == $orders->currentPage() + 3)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endfor

                    @if($orders->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $orders->currentPage() + 1 }}">{{ __('Sau') }}</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">{{ __('Sau') }}</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
@else
    <div class="alert alert-info text-center">
        <i class="ti ti-info-circle me-2"></i>
        {{ __('Không có lịch sử dùng điểm') }}
    </div>
@endif

