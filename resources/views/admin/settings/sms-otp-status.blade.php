@extends('admin.layouts.master')

@push('custom-css')
    <style>
        .metric-card {
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .metric-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .metric-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        .gradient-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-green {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .gradient-yellow {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        }

        .gradient-red {
            background: linear-gradient(135deg, #f857a6 0%, #ff5858 100%);
        }

        .gradient-teal {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .info-card {
            border-left: 4px solid #667eea;
        }

        .statistics-card {
            background: #f8f9fa;
            border-radius: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            @if ($error)
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">
                        <i class="ti ti-alert-circle me-2"></i>Lỗi
                    </h4>
                    <p>{{ $error }}</p>
                </div>
            @elseif ($data)
                <!-- Thông tin dự án -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-info-circle me-2"></i>Thông tin dự án
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Tên dự án:</strong>
                                        <p class="mb-0">{{ $data['project_name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Mã dự án:</strong>
                                        <p class="mb-0">{{ $data['project_code'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Loại thanh toán:</strong>
                                        <p class="mb-0">
                                            <span
                                                class="badge bg-{{ $data['billing_type'] == 'prepaid' ? 'primary' : 'success' }}">
                                                {{ $data['billing_type'] == 'prepaid' ? 'Trả trước' : 'Trả sau' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin ví và giá -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card metric-card gradient-teal text-white position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title metric-number">
                                            {{ number_format($data['wallet_balance'] ?? 0) }}
                                        </h4>
                                        <p class="card-text metric-label">Số dư ví</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-wallet metric-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card metric-card gradient-blue text-white position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title metric-number">
                                            {{ number_format($data['price_per_otp'] ?? 0) }}
                                        </h4>
                                        <p class="card-text metric-label">Giá mỗi OTP (VNĐ)</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-currency-dong metric-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card metric-card gradient-green text-white position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title metric-number">
                                            @if (isset($data['wallet_balance']) && isset($data['price_per_otp']) && $data['price_per_otp'] > 0)
                                                {{ number_format(floor($data['wallet_balance'] / $data['price_per_otp'])) }}
                                            @else
                                                0
                                            @endif
                                        </h4>
                                        <p class="card-text metric-label">Số OTP còn lại</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-message-circle metric-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê -->
                @if (isset($data['statistics']))
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card statistics-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ti ti-chart-bar me-2"></i>Thống kê
                                        <span class="badge bg-primary ms-2">
                                            {{ ucfirst($data['statistics']['period'] ?? 'month') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card mb-3">
                                                <div class="card-body text-center">
                                                    <h4 class="text-primary">
                                                        {{ number_format($data['statistics']['total_requests'] ?? 0) }}
                                                    </h4>
                                                    <p class="text-muted mb-0">Tổng yêu cầu</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card mb-3">
                                                <div class="card-body text-center">
                                                    <h4 class="text-success">
                                                        {{ number_format($data['statistics']['sent_count'] ?? 0) }}
                                                    </h4>
                                                    <p class="text-muted mb-0">Đã gửi</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card mb-3">
                                                <div class="card-body text-center">
                                                    <h4 class="text-danger">
                                                        {{ number_format($data['statistics']['failed_count'] ?? 0) }}
                                                    </h4>
                                                    <p class="text-muted mb-0">Thất bại</p>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $totalSentEver = $data['statistics']['total_sent_ever'] ?? 0;
                                            $lastBilledIndex = $data['statistics']['last_billed_index'] ?? 0;
                                            $pricePerOtp = $data['price_per_otp'] ?? 0;
                                            $currentSent = max(0, $totalSentEver - $lastBilledIndex);
                                            $currentCost = $currentSent * $pricePerOtp;
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="card mb-3">
                                                <div class="card-body text-center">
                                                    <h4 class="text-warning">
                                                        {{ number_format($currentCost) }}
                                                    </h4>
                                                    <p class="text-muted mb-0">
                                                        Tổng chi phí (VNĐ)
                                                        <small class="d-block text-muted">
                                                            (=(mới - cũ) × giá mỗi OTP)
                                                        </small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h4 class="text-info">
                                                        {{ number_format($data['statistics']['total_sent_ever'] ?? 0) }}
                                                    </h4>
                                                    <p class="text-muted mb-0">Tổng đã gửi (tất cả thời gian)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h4 class="text-secondary">
                                                        {{ number_format($data['statistics']['last_billed_index'] ?? 0) }}
                                                    </h4>
                                                    <p class="text-muted mb-0">Chỉ số cũ không được tính</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="text-muted mb-1">Tỷ lệ thành công</p>
                                                            @php
                                                                $totalRequests =
                                                                    $data['statistics']['total_requests'] ?? 0;
                                                                $sentCount = $data['statistics']['sent_count'] ?? 0;
                                                                $successRate =
                                                                    $totalRequests > 0
                                                                        ? ($sentCount / $totalRequests) * 100
                                                                        : 0;
                                                            @endphp
                                                            <h4 class="mb-0">
                                                                {{ number_format($successRate, 2) }}%
                                                            </h4>
                                                        </div>
                                                        <div class="progress" style="width: 100px; height: 100px;">
                                                            <svg class="progress-ring" width="100" height="100">
                                                                <circle class="progress-ring-circle" stroke="#667eea"
                                                                    stroke-width="8" fill="transparent" r="42"
                                                                    cx="50" cy="50"
                                                                    stroke-dasharray="{{ 2 * pi() * 42 }}"
                                                                    stroke-dashoffset="{{ 2 * pi() * 42 * (1 - $successRate / 100) }}"
                                                                    transform="rotate(-90 50 50)" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Nút refresh -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="{{ route('admin.setting.smsOtpStatus') }}" class="btn btn-primary">
                                    <i class="ti ti-refresh me-2"></i>Làm mới dữ liệu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">
                        <i class="ti ti-info-circle me-2"></i>Không có dữ liệu
                    </h4>
                    <p>Không thể lấy thông tin SMS OTP. Vui lòng kiểm tra lại cấu hình API_KEY trong file .env</p>
                </div>
            @endif
        </div>
    </div>
@endsection
