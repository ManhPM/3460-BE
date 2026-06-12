<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #f0f2f5;
        overflow-x: hidden;
    }

    /* Topbar */
    .topbar {
        height: 64px;
        background: linear-gradient(135deg, #E3F2FF, #4DA3FF);
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .menu-toggle {
        background: none;
        border: none;
        font-size: 20px;
        color: black;
        cursor: pointer;
        padding: 8px;
        transition: color 0.2s;
    }

    .menu-toggle:hover {
        color: black;
    }

    .brand {
        font-size: 18px;
        font-weight: 600;
        color: black;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .topbar-icon {
        background: none;
        border: none;
        color: black;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        position: relative;
    }

    .topbar-icon:hover {
        background: #f3f4f6;
        color: black;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    /* User Dropdown */
    .user-dropdown {
        position: fixed;
        top: 70px;
        right: 1.5rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        min-width: 320px;
        display: none;
        z-index: 1002;
        overflow: hidden;
        animation: dropdownFadeIn 0.3s ease;
    }

    .user-dropdown.active {
        display: block;
    }

    .user-dropdown-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-dropdown-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .user-dropdown-info h4 {
        font-size: 16px;
        font-weight: 600;
        color: black;
        margin-bottom: 4px;
    }

    .user-dropdown-info p {
        font-size: 13px;
        color: #6c757d;
    }

    .settings-icon-user {
        background: #f3f4f6;
        border: none;
        color: #6c757d;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        margin-left: auto;
    }

    .settings-icon-user:hover {
        background: #e5e7eb;
        color: black;
    }

    .user-dropdown-menu {
        padding: 0.5rem 0;
    }

    .user-dropdown-item {
        padding: 0.875rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #4b5563;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .user-dropdown-item:hover {
        background: #f9fafb;
        color: black;
    }

    .user-dropdown-item i {
        width: 20px;
        font-size: 16px;
        color: #9ca3af;
    }

    .user-dropdown-item:hover i {
        color: #6c757d;
    }

    .message-dropdown {
        position: fixed;
        /* Luôn bám màn hình */
        top: 70px;
        /* cách top 70px */
        right: 1.5rem;
        /* cách phải 1.5rem */
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        width: 400px;
        max-height: 500px;
        display: none;
        z-index: 1002;
        overflow: hidden;
        animation: dropdownFadeIn 0.3s ease;
    }

    .message-dropdown.active {
        display: block;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-header h3 {
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .message-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .message-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        gap: 12px;
    }

    .message-item:hover {
        background: #f9fafb;
    }

    .message-item:last-child {
        border-bottom: none;
    }

    .message-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .message-content {
        flex: 1;
        min-width: 0;
    }

    .message-title {
        font-size: 14px;
        font-weight: 500;
        color: black;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .message-meta {
        font-size: 12px;
        color: #9ca3af;
    }

    /* Sidebar */
    .sidebar {
        width: 300px;
        height: calc(100vh - 64px);
        background: linear-gradient(45deg, #1A4FFF, #4DE0F6);
        border-right: 1px solid #e3e6f0;
        position: fixed;
        top: 64px;
        left: 0;
        overflow-y: auto;
        padding: 1rem 0 2rem 0;
        transition: transform 0.3s ease;
        z-index: 999;
    }

    .sidebar {
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* IE, Edge cũ */
    }

    /* Chrome, Safari */
    .sidebar::-webkit-scrollbar {
        display: none;
    }

    .sidebar.hidden {
        transform: translateX(-100%);
    }

    .sidebar-section {
        margin-bottom: 0.5rem;
    }

    .section-title {
        font-size: 11px;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.5rem 1.5rem;
        margin-bottom: 0.25rem;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        color: white;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }

    .nav-item.expanded .nav-link .nav-text,
    .nav-item.expanded .sub-menu .sub-nav-item {
        color: #1a1d21;
    }

    .nav-item.expanded .nav-link i,
    .nav-item.expanded .sub-menu i {
        color: #1a1d21;
    }

    .nav-link:hover {
        background: #f9fafb;
        color: black;
    }

    .nav-link i.nav-icon {
        width: 20px;
        font-size: 16px;
        color: black;
        flex-shrink: 0;
    }

    .nav-link .nav-text {
        flex: 1;
    }

    .nav-link .fa-chevron-down {
        font-size: 11px;
        transition: transform 0.3s ease;
    }

    .nav-item.expanded .nav-link .fa-chevron-down {
        transform: rotate(180deg);
    }

    .sub-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: #f9fafb;
    }

    .nav-item.expanded .sub-menu {
        max-height: 500px;
    }

    .sub-nav-item {
        padding: 0.625rem 1.5rem 0.625rem 3.5rem;
        color: #6c757d;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sub-nav-item:hover {
        color: black;
        background: #f3f4f6;
    }

    .user-info {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e3e6f0;
        margin-top: 1rem;
    }

    .user-info-label {
        font-size: 11px;
        color: #9ca3af;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .user-info-name {
        font-weight: 600;
        font-size: 14px;
        color: black;
    }

    .user-info-email {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }

    /* Main Content */
    .main-content {
        margin-left: 300px;
        margin-top: 64px;
        transition: margin-left 0.3s ease;
    }

    .main-content.expanded {
        margin-left: 0;
    }

    .hero-section {
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
        color: white;
        margin-bottom: 2rem;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .hero-section p {
        font-size: 1.1rem;
        opacity: 0.95;
    }

    /* Overlay */
    .overlay {
        display: none;
        position: fixed;
        top: 64px;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .overlay.active {
        display: block;
        opacity: 1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .topbar {
            padding: 0 1rem;
        }

        .brand {
            font-size: 16px;
        }

        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .hero-section {
            padding: 3rem 1.5rem;
        }

        .hero-section h1 {
            font-size: 1.75rem;
        }

        .hero-section p {
            font-size: 1rem;
        }

        .message-dropdown {
            width: calc(100vw - 2rem);
            right: 1rem;
        }

        .notification-dropdown {
            width: calc(100vw - 2rem);
            right: 1rem;
        }

        .user-dropdown {
            width: calc(100vw - 2rem);
            right: 1rem;
        }
    }

    @media (max-width: 480px) {
        .topbar-right {
            gap: 0.25rem;
        }

        .topbar-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
        }

        .brand {
            display: none;
        }

        .hero-section h1 {
            font-size: 1.5rem;
        }
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script>
    let isMobile = window.innerWidth <= 768;
    let sidebarOpen = !isMobile;

    window.addEventListener('resize', function() {
        isMobile = window.innerWidth <= 768;
        if (!isMobile) {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
            sidebarOpen = true;
        } else {
            sidebarOpen = false;
        }
        updateLayout();
    });

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (isMobile) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        } else {
            sidebarOpen = !sidebarOpen;
            updateLayout();
        }
    }

    function closeSidebar() {
        if (isMobile) {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }
    }

    function updateLayout() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        if (!isMobile && sidebar && mainContent) {
            if (sidebarOpen) {
                sidebar.classList.remove('hidden');
                mainContent.classList.remove('expanded');
            } else {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
            }
        }
    }

    function toggleMenu(element) {
        const navItem = element.closest('.nav-item');
        const isExpanded = navItem.classList.contains('expanded');

        // Đóng tất cả menu khác
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item !== navItem) {
                item.classList.remove('expanded');
            }
        });

        // Toggle menu hiện tại
        navItem.classList.toggle('expanded');
    }

    function toggleMessages() {
        const dropdown = document.getElementById('messageDropdown');
        dropdown.classList.toggle('active');
    }

    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('active');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const messageDropdown = document.getElementById('messageDropdown');
        const userDropdown = document.getElementById('userDropdown');
        const messageButton = event.target.closest('.topbar-icon');
        const userAvatar = event.target.closest('.user-avatar');

        // Close message dropdown if click outside
        if (!messageButton && !messageDropdown.contains(event.target) && !event.target.closest(
                '#messageDropdown')) {
            messageDropdown.classList.remove('active');
        }

        // Close user dropdown if click outside
        if (!userAvatar && !userDropdown.contains(event.target) && !event.target.closest('#userDropdown')) {
            userDropdown.classList.remove('active');
        }
    });

    // Initialize layout
    updateLayout();
</script>
