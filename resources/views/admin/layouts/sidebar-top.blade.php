<div class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="brand">Hi {{ auth('admin')->user()->fullname }}</div>
    </div>
    <div class="topbar-right">
        <button class="topbar-icon position-relative" id="notificationBell" onclick="toggleMessages()">
            <span style="position: relative; display: inline-block;">
                🔔
                <span id="notification-badge"
                    class="badge bg-danger rounded-pill position-absolute translate-middle top-0 start-100"
                    style="font-size: 0.7em; min-width: 18px; min-height: 18px; display: none;">
                </span>
            </span>
        </button>
        <div class="user-avatar" onclick="toggleUserDropdown()" id="userAvatar">
            <img src="{{ asset(auth('admin')->user()->avatar) }}" alt="User">
        </div>
    </div>
</div>

<!-- Message Dropdown -->
<div class="message-dropdown" id="messageDropdown">
    <div class="dropdown-header">
        <h3>
            <i class="ti ti-bell-ringing"></i>
            Thông báo
        </h3>
    </div>
    <div class="message-list" id="notification-list">
        <div class="text-center text-muted py-3" id="notification-empty" style="display:none;">
            Không có thông báo
        </div>
        <!-- Notifications will be injected here -->
    </div>
    <div class="d-flex flex-column" id="notification-actions" style="display:none;">
        <a href="{{ route('admin.notification.readAllNotification') }}"
            class="dropdown-item bg-white text-center justify-content-center text-dark fw-bold p-3"
            style="cursor: pointer;">
            Đọc tất cả
        </a>
        <a href="{{ route('admin.notification.getAllNotificationAdminView') }}"
            class="dropdown-item bg-white text-center justify-content-center text-dark fw-bold p-3"
            style="cursor: pointer;">
            Tất cả thông báo
        </a>
    </div>
</div>

<script>
    // Notification fetch and render
    function renderNotificationItem(notification) {
        const isRead = notification.read_at !== null;
        const bgColor = isRead ? 'bg-light' : 'bg-white';
        const badge = isRead ?
            '<span class="badge bg-success ms-2">Đã đọc</span>' :
            '<span class="badge bg-danger ms-2">Chưa đọc</span>';
        const receivedAt = new Date(notification.created_at).toLocaleString('vi-VN');
        const readAt = notification.read_at ? new Date(notification.read_at).toLocaleString('vi-VN') : 'Chưa đọc';

        return `
            <div class="message-item ${bgColor} dropdown-item-notification"
                 style="cursor:pointer;"
                 data-id="${notification.id}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="left"
                 title="${notification.short_message || 'Không có nội dung'}">
                <div class="message-content">
                    <div class="message-title d-flex align-items-center">
                        ${notification.title} ${badge}
                    </div>
                    <div class="message-meta">
                        <small class="text-muted">Nhận lúc: ${receivedAt}</small><br>
                        <small class="text-muted">Đọc lúc: ${readAt}</small>
                    </div>
                </div>
            </div>
        `;
    }

    function fetchNotifications() {
        $.ajax({
            url: "{{ route('admin.notification.getAllNotificationAdmin') }}",
            method: "GET",
            success: function(response) {
                const $badge = $('#notification-badge');
                const $list = $('#notification-list');
                const $empty = $('#notification-empty');
                const $actions = $('#notification-actions');

                // Badge
                if (response.count > 0) {
                    $badge.text(response.count > 9 ? '9+' : response.count).show();
                } else {
                    $badge.text('0').show();
                }

                // List
                $list.empty();
                if (!response.data || response.data.length === 0) {
                    $empty.show();
                    $actions.hide();
                } else {
                    $empty.hide();
                    response.data.forEach(function(notification) {
                        $list.append(renderNotificationItem(notification));
                    });
                    $actions.show();
                }

                // Tooltip
                var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Click event
                $('.dropdown-item-notification').on('click', function() {
                    const notificationId = $(this).data('id');
                    window.location.href =
                        "{{ route('admin.notification.show', ['id' => '__ID__']) }}"
                        .replace('__ID__', notificationId);
                });
            },
            error: function() {
                $('#notification-list').html(
                    '<div class="text-center text-danger py-3">Có lỗi xảy ra khi lấy danh sách thông báo.</div>'
                );
                $('#notification-badge').hide();
            }
        });
    }

    // Toggle message dropdown
    function toggleMessages() {
        const dropdown = document.getElementById('messageDropdown');
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
            fetchNotifications();
        }
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('messageDropdown');
        const bell = document.getElementById('notificationBell');
        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Hide dropdown on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('messageDropdown').style.display = 'none';
        }
    });

    // Optional: fetch notifications on page load to show badge
    document.addEventListener('DOMContentLoaded', function() {
        fetchNotifications();
    });
</script>

<!-- User Dropdown -->
<div class="user-dropdown" id="userDropdown">
    <div class="user-dropdown-header">
        <img src="{{ asset(auth('admin')->user()->avatar) }}" alt="User" class="user-dropdown-avatar">
        <div class="user-dropdown-info">
            <h4>{{ auth('admin')->user()->fullname }}</h4>
            <p>{{ auth('admin')->user()->email }}</p>
        </div>
    </div>
    <div class="user-dropdown-menu">
        <a class="user-dropdown-item" href="{{ route('admin.profile.index') }}" style="text-decoration: none;"
            onmouseover="this.style.background='#f3f4f6';this.style.textDecoration='none';"
            onmouseout="this.style.background='';this.style.textDecoration='none';">
            <i class="ti ti-user-edit"></i>
            {{ __('Tài khoản') }}
        </a>
        <a class="user-dropdown-item" href="{{ route('admin.password.index') }}" style="text-decoration: none;"
            onmouseover="this.style.background='#f3f4f6';this.style.textDecoration='none';"
            onmouseout="this.style.background='';this.style.textDecoration='none';">
            <i class="ti ti-lock"></i>
            {{ __('Đổi mật khẩu') }}
        </a>
        <a class="user-dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalLogout"
            style="text-decoration: none;"
            onmouseover="this.style.background='#f3f4f6';this.style.textDecoration='none';"
            onmouseout="this.style.background='';this.style.textDecoration='none';">
            <i class="ti ti-logout"></i>
            {{ __('Đăng xuất') }}
        </a>
    </div>
</div>
