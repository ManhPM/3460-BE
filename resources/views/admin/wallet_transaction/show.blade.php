@extends('admin.layouts.master')

@push('libs-css')
@endpush

@push('custom-css')
    <style>
        .info-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 180px;
            font-size: 0.875rem;
        }

        .info-value {
            color: #212529;
            font-size: 0.95rem;
        }

        .proof-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                            <h2 class="mb-0 fs-2">
                                <i class="ti ti-file-invoice me-2"></i>
                                {{ __('Chi tiết giao dịch ví') }} #{{ $transaction->id }}
                            </h2>
                            <div>
                                <a href="{{ route('admin.wallet_transaction.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ti ti-arrow-left me-1"></i>
                                    {{ __('Quay lại') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <!-- Thông tin cơ bản -->
                                <div class="col-12 col-md-6">
                                    <div class="info-card">
                                        <h5 class="mb-3 fw-semibold">
                                            <i class="ti ti-info-circle me-2"></i>
                                            {{ __('Thông tin cơ bản') }}
                                        </h5>

                                        <div class="info-item">
                                            <div class="info-label">{{ __('ID giao dịch') }}:</div>
                                            <div class="info-value">#{{ $transaction->id }}</div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">{{ __('Khách hàng') }}:</div>
                                            <div class="info-value">
                                                @if ($transaction->user)
                                                    <a href="{{ route('admin.user.edit', $transaction->user->id) }}"
                                                        class="fw-medium text-primary">
                                                        {{ $transaction->user->fullname }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">{{ __('Số tiền') }}:</div>
                                            <div class="info-value">
                                                <span
                                                    class="fs-5 fw-semibold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ format_price($transaction->amount) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">{{ __('Loại giao dịch') }}:</div>
                                            <div class="info-value">
                                                @php
                                                    $typeEnum = \App\Enums\Transaction\WalletTransactionType::from(
                                                        $transaction->type,
                                                    );
                                                @endphp
                                                <span class="badge {{ $typeEnum->badge() }}">
                                                    {{ \App\Enums\Transaction\WalletTransactionType::getDescription($transaction->type) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">{{ __('Trạng thái') }}:</div>
                                            <div class="info-value">
                                                @php
                                                    $statusEnum = \App\Enums\Transaction\WalletTransactionStatus::from(
                                                        $transaction->status,
                                                    );
                                                @endphp
                                                <span class="badge {{ $statusEnum->badge() }}">
                                                    {{ \App\Enums\Transaction\WalletTransactionStatus::getDescription($transaction->status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-label">{{ __('Ngày tạo') }}:</div>
                                            <div class="info-value">{{ format_datetime($transaction->created_at) }}</div>
                                        </div>

                                        @if ($transaction->updated_at && $transaction->updated_at != $transaction->created_at)
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Ngày cập nhật') }}:</div>
                                                <div class="info-value">{{ format_datetime($transaction->updated_at) }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Thông tin chi tiết -->
                                <div class="col-12 col-md-6">
                                    <div class="info-card">
                                        <h5 class="mb-3 fw-semibold">
                                            <i class="ti ti-file-text me-2"></i>
                                            {{ __('Thông tin chi tiết') }}
                                        </h5>

                                        @if ($transaction->note)
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Ghi chú') }}:</div>
                                                <div class="info-value">{{ $transaction->note }}</div>
                                            </div>
                                        @endif

                                        @if ($transaction->order_id)
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Mã đơn hàng') }}:</div>
                                                <div class="info-value">
                                                    <a href="{{ route('admin.order.edit', $transaction->order_id) }}"
                                                        class="fw-medium text-primary">
                                                        #{{ $transaction->order_id }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($transaction->proof_image)
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Ảnh chứng từ') }}:</div>
                                                <div class="info-value">
                                                    <a href="{{ asset($transaction->proof_image) }}" target="_blank"
                                                        class="d-inline-block">
                                                        <img src="{{ asset($transaction->proof_image) }}" alt="Proof Image"
                                                            class="proof-image">
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Nút duyệt/hủy -->
                            @if ($transaction->status === 'pending')
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">
                                                    <i class="ti ti-shield-check me-2"></i>
                                                    {{ __('Thao tác') }}
                                                </h5>
                                                <div class="d-flex gap-2">
                                                    <form
                                                        action="{{ route('admin.wallet_transaction.approve', $transaction->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-lg"
                                                            onclick="return confirm('Bạn có chắc chắn muốn duyệt giao dịch này?');">
                                                            <i class="ti ti-check me-2"></i>
                                                            {{ __('Duyệt giao dịch') }}
                                                        </button>
                                                    </form>
                                                    <form
                                                        action="{{ route('admin.wallet_transaction.reject', $transaction->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-lg"
                                                            onclick="return confirm('Bạn có chắc chắn muốn hủy giao dịch này?');">
                                                            <i class="ti ti-x me-2"></i>
                                                            {{ __('Hủy giao dịch') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <i class="ti ti-info-circle me-2"></i>
                                            {{ __('Giao dịch này đã được xử lý và không thể thay đổi trạng thái.') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
