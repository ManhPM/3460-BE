@extends('admin.layouts.master')

<style>
    .pagination {
        display: flex;
        margin-bottom: 1em;
        justify-content: center;
        align-items: center;
    }

    .pagination-btn {
        border: 1px solid #ccc;
        border-radius: 50% 50%;
        padding: 5px 12px;
        margin: 0 5px;
        background-color: #fff;
        color: #000;
        cursor: pointer;
    }

    .pagination-btn:hover {
        background-color: #1c5639;
        color: #fff;
    }

    .pagination-btn.active {
        background-color: #1c5639;
        color: #fff;
    }

    .pagination-btn.prev,
    .pagination-btn.next {
        cursor: pointer;
        padding: 5px 8px
    }

    .pagination-btn[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .notification-list {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .notification-list-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-list-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .notification-stats {
        color: #666;
        font-size: 1.1em;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
        cursor: pointer;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-item.unread {
        background-color: #fff8e1;
        border-left: 3px solid #ffa726;
    }

    .notification-item.unread:hover {
        background-color: #fff3c4;
    }

    .notification-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .notification-item-title {
        font-size: 1.2rem;
        font-weight: 500;
        color: #333;
        margin: 0;
        flex: 1;
        margin-right: 10px;
    }

    .notification-item-time {
        color: #666;
        font-size: 1em;
        white-space: nowrap;
    }

    .notification-item-content {
        color: #666;
        font-size: 1.1em;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notification-status {
        display: inline-flex;
        align-items: center;
        font-size: 1em;
        margin-top: 5px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.9em;
        font-weight: 500;
    }

    .status-unread {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .status-read {
        background-color: #e8f5e8;
        color: #388e3c;
    }

    .pagination-wrapper {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state i {
        font-size: 3.5rem;
        color: #ddd;
        margin-bottom: 15px;
    }

    .empty-state h3 {
        margin-bottom: 10px;
        color: #666;
        font-size: 1.3rem;
    }

    .empty-state p {
        font-size: 1.1em;
    }

    /* Custom pagination styles */
    .pagination {
        margin: 0;
    }

    .page-link {
        color: #007bff;
        border-color: #dee2e6;
        font-size: 1em;
        padding: 8px 12px;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
</style>

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="notification-list">
                <div class="notification-list-header">
                    <h2 class="notification-list-title">{{ __('Danh sách thông báo') }}</h2>
                    <div class="notification-stats">
                        {{ __('Tổng cộng') }}: {{ $notifications->total() }} {{ __('thông báo') }}
                    </div>
                </div>

                @if ($notifications->count() > 0)
                    <!-- Notification items -->
                    @foreach ($notifications as $notification)
                        <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}"
                            onclick="window.location.href='{{ route('admin.notification.show', $notification->id) }}'">
                            <div class="notification-item-header">
                                <h3 class="notification-item-title">
                                    {{ $notification->title }}
                                </h3>
                                <div class="notification-item-time">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('H:i d-m-Y') }}
                                </div>
                            </div>
                            <div class="notification-item-content">
                                {{ $notification->short_message }}
                            </div>
                            <div class="notification-status">
                                @if (is_null($notification->read_at))
                                    <span class="status-badge status-unread">
                                        <i class="ti ti-circle-filled me-1"></i>{{ __('Chưa đọc') }}
                                    </span>
                                @else
                                    <span class="status-badge status-read">
                                        <i class="ti ti-check me-1"></i>{{ __('Đã đọc') }}
                                    </span>
                                    <span class="ms-2 text-muted" style="font-size: 1em;">
                                        {{ __('Đọc lúc') }}:
                                        {{ \Carbon\Carbon::parse($notification->read_at)->format('H:i d-m-Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($notifications && $notifications->hasPages())
                        <div class="pagination-wrapper">
                            <!-- Nút Previous -->
                            @if (!$notifications->onFirstPage())
                                <button class="pagination-btn prev"
                                    onclick="location.href='{{ $notifications->previousPageUrl() }}'">
                                    <i class="ti ti-arrow-left" aria-hidden="true"></i>
                                </button>
                            @endif

                            <!-- Nút phân trang -->
                            @if ($notifications->currentPage() > 3)
                                <button class="pagination-btn"
                                    onclick="location.href='{{ $notifications->url(1) }}'">1</button>
                                @if ($notifications->currentPage() > 4)
                                    <span class="pagination-ellipsis">...</span>
                                @endif
                            @endif

                            @foreach (range(1, $notifications->lastPage()) as $i)
                                @if ($i >= $notifications->currentPage() - 2 && $i <= $notifications->currentPage() + 2)
                                    @if ($i == $notifications->currentPage())
                                        <button class="pagination-btn active" disabled>
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button onclick="location.href='{{ $notifications->url($i) }}'"
                                            class="pagination-btn">
                                            {{ $i }}
                                        </button>
                                    @endif
                                @endif
                            @endforeach

                            @if ($notifications->currentPage() < $notifications->lastPage() - 2)
                                @if ($notifications->currentPage() < $notifications->lastPage() - 3)
                                    <span class="pagination-ellipsis">...</span>
                                @endif
                                <button class="pagination-btn"
                                    onclick="location.href='{{ $notifications->url($notifications->lastPage()) }}'">{{ $notifications->lastPage() }}</button>
                            @endif

                            <!-- Nút Next -->
                            @if ($notifications->hasMorePages())
                                <button class="pagination-btn next"
                                    onclick="location.href='{{ $notifications->nextPageUrl() }}'">
                                    <i class="ti ti-arrow-right" aria-hidden="true"></i>
                                </button>
                            @endif
                        </div>
                    @endif
                @else
                    <!-- Empty state -->
                    <div class="empty-state">
                        <i class="ti ti-bell-off"></i>
                        <h3>{{ __('Không có thông báo nào') }}</h3>
                        <p>{{ __('Bạn chưa có thông báo nào trong hệ thống.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
