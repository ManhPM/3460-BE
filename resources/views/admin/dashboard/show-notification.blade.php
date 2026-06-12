@extends('admin.layouts.master')

<style>
    .notification-detail {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .notification-detail-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
    }

    .notification-detail-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .notification-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #666;
        font-size: 0.9em;
    }

    .notification-detail-content {
        padding: 20px;
        color: #444;
        line-height: 1.6;
    }

    .notification-actions {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-back {
        padding: 8px 16px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-back:hover {
        background: #e9ecef;
        text-decoration: none;
    }
</style>
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="notification-detail">
                <div class="notification-detail-header">
                    <div class="notification-detail-title">
                        {{ $notification->title }}
                    </div>
                    <div class="notification-meta">
                        <div class="notification-time">
                            Thời gian nhận:
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('H:i d-m-Y') }}
                        </div>
                    </div>
                    <div class="notification-meta">
                        <div class="notification-time">
                            Thời gian đọc:
                            {{ \Carbon\Carbon::parse($notification->read_at)->format('H:i d-m-Y') }}
                        </div>
                    </div>
                </div>
                <div class="notification-detail-content">
                    {!! $notification->message !!}
                </div>
                <div class="notification-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn-back">
                        <i class="ti ti-arrow-left me-2"></i>{{ __('Quay lại danh sách') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
