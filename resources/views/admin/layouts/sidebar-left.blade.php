<link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
<style>
    .btn-default-cms {
        background: linear-gradient(135deg,
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_1')->plain_value }},
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_2')->plain_value }});
        color: #ffffff;
    }

    .btn-default-cms:hover {
        color: #ffffff;
    }

    .page-wrapper {
        background-color: {{ $settings->firstWhere('setting_key', 'bg_color')->plain_value }};
    }

    .icon-bell {
        font-size: 1.5em;
        right: -3em;
        color: {{ $settings->firstWhere('setting_key', 'top_sidebar_text_color')->plain_value }};
        transition: color 0.3s ease;
    }

    #header {
        background: linear-gradient(135deg,
                {{ $settings->firstWhere('setting_key', 'top_sidebar_bg_color_1')->plain_value }},
                {{ $settings->firstWhere('setting_key', 'top_sidebar_bg_color_2')->plain_value }});
        color: {{ $settings->firstWhere('setting_key', 'top_sidebar_text_color')->plain_value }};
    }

    .fancy-breadcrumb {
        width: 100%;
    }

    .breadcrumb-list {
        display: flex;
        flex-wrap: wrap;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .breadcrumb-item {
        position: relative;
        display: flex;
        align-items: center;
    }

    .breadcrumb-item:not(:last-child)::after {
        content: '→';
        margin: 0 0.5rem;
        color: black;
        animation: fadeIn 0.5s ease-in;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        text-decoration: none;
        color: {{ $settings->firstWhere('setting_key', 'breadcrumbs_text_color')->plain_value }};
        background: linear-gradient(135deg,
                {{ $settings->firstWhere('setting_key', 'breadcrumbs_bg_color_1')->plain_value }},
                {{ $settings->firstWhere('setting_key', 'breadcrumbs_bg_color_2')->plain_value }});
        border-radius: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .breadcrumb-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .breadcrumb-icon {
        margin-right: 0.5rem;
        font-size: 1.1em;
    }

    .breadcrumb-text {
        font-weight: 500;
    }

    .breadcrumb-item.active .breadcrumb-link {
        pointer-events: none;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .breadcrumb-item {
        animation: slideIn 0.5s ease-out forwards;
        opacity: 0;
    }

    .breadcrumb-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .breadcrumb-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .breadcrumb-item:nth-child(3) {
        animation-delay: 0.3s;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Mobile Overlay */
    .mobile-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .mobile-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    #searchMenuInput {
        background-color: #fff;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 10px;
        color: black;
    }

    /* Navbar */
    .navbar-vertical {
        background: linear-gradient(135deg,
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_1')->plain_value }},
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_2')->plain_value }});
        color: #fff;
        transition: all 0.3s ease;
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        z-index: 1050;
        overflow-y: auto;
    }

    /* Desktop behavior */
    @media (min-width: 1200px) {

        /* Tăng kích thước sidebar */
        .navbar-vertical.navbar-expand-xl,
        .navbar-vertical.navbar-expand-lg {
            width: 17rem !important;
        }

        .navbar-expand-lg.navbar-vertical~.navbar,
        .navbar-expand-lg.navbar-vertical~.page-wrapper,
        .navbar-expand-xl.navbar-vertical~.navbar,
        .navbar-expand-xl.navbar-vertical~.page-wrapper {
            margin-left: 17rem !important;
        }

        .navbar-vertical {
            position: relative;
            transform: translateX(0);
        }

        .navbar-vertical.sidebar-collapsed {
            width: 0 !important;
            overflow: hidden;
        }

        .navbar-vertical.sidebar-collapsed .navbar-collapse {
            display: none;
        }
    }

    /* Mobile and Tablet behavior */
    @media (max-width: 1199px) {
        .navbar-vertical {
            transform: translateX(-100%);
        }

        .navbar-vertical.show {
            transform: translateX(0);
        }

        /* Hide logo on mobile/tablet */
        .navbar-brand {
            display: none !important;
        }

        /* Adjust sidebar content padding */
        .navbar-vertical .container-fluid {
            padding-top: 1rem;
        }

        /* Hide desktop toggle button */
        .sidebar-toggle-btn {
            display: none !important;
        }
    }

    /* Mobile toggle button */
    .mobile-toggle-btn {
        display: none;
        z-index: 1060;
        background: linear-gradient(135deg,
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_1')->plain_value }},
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_2')->plain_value }});
        border: none;
        border-radius: 8px;
        padding: 0.75rem;
        color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .mobile-toggle-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 1199px) {
        .mobile-toggle-btn {
            display: block;
        }
    }

    .navbar-vertical.navbar-expand-lg .navbar-collapse .dropdown-menu .dropdown-item.active,
    .navbar-vertical.navbar-expand-lg .navbar-collapse .nav-link.active {
        background: {{ $settings->firstWhere('setting_key', 'left_sidebar_selected_color')->plain_value }} !important;
    }

    .navbar-vertical.navbar-expand-lg .navbar-collapse .dropdown-menu .dropdown-item.active .nav-link-title,
    .navbar-vertical.navbar-expand-lg .navbar-collapse .dropdown-menu .dropdown-item.active .nav-link-icon,
    .navbar-vertical.navbar-expand-lg .navbar-collapse .nav-link.active .nav-link-title,
    .navbar-vertical.navbar-expand-lg .navbar-collapse .nav-link.active .nav-link-icon {
        color: {{ $settings->firstWhere('setting_key', 'left_sidebar_selected_text_color')->plain_value }} !important;
    }

    .navbar-brand img {
        transition: transform 0.3s ease;
    }

    /* Collapse sidebar on smaller screens */
    .navbar-collapse.collapse {
        transition: all 0.3s ease;
        animation: collapseExpand 0.3s ease-in-out;
    }

    @keyframes collapseExpand {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(0);
        }
    }

    /* Nav items */
    .navbar-nav .nav-item {
        border-radius: 8px;
        transition: background-color 0.3s ease, color 0.3s ease;
        display: flex;
    }

    /* Nav link icon */
    .nav-link-icon {
        margin-right: 10px;
        margin-bottom: 5px;
        font-size: 1.4em;
        color: {{ $settings->firstWhere('setting_key', 'left_sidebar_text_color')->plain_value }};
        transition: transform 0.3s ease, color 0.3s ease;
    }

    /* Nav link title */
    .nav-link-title {
        font-size: 0.8rem;
        font-weight: 500;
        color: {{ $settings->firstWhere('setting_key', 'left_sidebar_text_color')->plain_value }};
        transition: color 0.3s ease;
    }

    /* Hover effects */
    .navbar-nav .nav-item:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .navbar-nav .nav-item:hover .nav-link-title,
    .navbar-nav .nav-item:hover .nav-link-icon {
        color: #cbd5e1;
    }

    .dropdown-menu {
        background: rgba(0, 0, 0, 0.5);
        border: none;
        animation: fadeIn 0.3s ease;
        transform-origin: top center;
    }

    /* Submenu items */
    .dropdown-item {
        color: #fff;
        transition: color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #c4b5fd;
    }

    /* Desktop Toggle Sidebar */
    .sidebar-toggle-btn {
        position: fixed;
        top: 50%;
        transform: translateY(-800%) rotate(-90deg);
        left: 13.6rem;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg,
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_1')->plain_value }},
                {{ $settings->firstWhere('setting_key', 'left_sidebar_bg_color_2')->plain_value }});
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        transition: all 0.3s;
    }

    .sidebar-toggle-btn:hover {
        background-color: #495057;
    }

    .sidebar-toggle-btn .arrow-icon {
        font-size: 16px;
        transition: transform 0.3s;
    }

    .sidebar-collapsed .sidebar-toggle-btn .arrow-icon {
        transform: rotate(180deg);
    }

    .arrow-icon {
        transition: transform 0.3s ease;
    }

    .arrow-left {
        transform: rotate(60deg);
    }

    .arrow-right {
        transform: rotate(-120deg);
    }

    /* Responsive cho tablet trở xuống */
    @media (max-width: 992px) {
        .fancy-breadcrumb .breadcrumb-list {
            flex-direction: column;
            align-items: flex-start;
        }

        .fancy-breadcrumb .breadcrumb-item {
            margin-right: 0;
            margin-bottom: 0.5rem;
        }

        .fancy-breadcrumb .breadcrumb-item:not(:last-child)::after {
            display: none;
        }

        .fancy-breadcrumb .breadcrumb-link,
        .fancy-breadcrumb .breadcrumb-item.active .breadcrumb-link {
            padding: 0.25rem 0.5rem;
            background-color: #f3f4f6;
            border-radius: 0.25rem;
        }

        .fancy-breadcrumb .breadcrumb-link:hover {
            background-color: #e5e7eb;
        }
    }
</style>

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<style>
    /* Thêm CSS cho item đang active */
    .nav-link.active,
    .sub-nav-item.active {
        background: #e0f2fe !important;
        color: #0284c7 !important;
        font-weight: 600;
    }

    .nav-link.active .nav-icon,
    .sub-nav-item.active .nav-icon {
        color: #0284c7 !important;
    }

    .nav-link.active .nav-text,
    .sub-nav-item.active .nav-text {
        color: #0284c7 !important;
    }

    .nav-item.expanded>.nav-link {
        background: #f0f9ff;
    }

    /* Fix cho drag to new tab */
    .nav-link,
    .sub-nav-item {
        cursor: pointer;
        user-select: none;
        -webkit-user-drag: element;
    }
</style>

<div class="sidebar" id="sidebar">

    <div style="display: flex; justify-content: center; align-items: center; padding: 1rem;">
        <img src="{{ asset($settings->firstWhere('setting_key', 'site_logo')->plain_value) }}" alt="logo"
            class="logo" style="max-height: 150px; width: auto; object-fit: contain;">
    </div>
    <!-- Search box for modules -->
    <div class="sidebar-search" style="padding: 1rem 1rem 0.5rem 1rem;">
        <input type="text" id="sidebar-search-input" class="form-control" placeholder="Tìm kiếm module..."
            style="font-size: 1em; border-radius: 0.5em;">
    </div>
    @php
        $currentRoute = \Route::currentRouteName();
    @endphp
    <div id="sidebar-menu-list">
        @foreach ($menu as $item)
            @if (auth('admin')->user()->checkPermissions($item['permissions']) || in_array('mevivuDev', $item['permissions']))
                @php
                    // Kiểm tra active cho item cha
                    $isActive = false;
                    if (!empty($item['routeName']) && $currentRoute === $item['routeName']) {
                        $isActive = true;
                    } elseif (!empty($item['sub'])) {
                        foreach ($item['sub'] as $subItem) {
                            if (!empty($subItem['routeName']) && $currentRoute === $subItem['routeName']) {
                                $isActive = true;
                                break;
                            }
                        }
                    }
                    $hasSub = count($item['sub']) > 0;
                @endphp
                <div class="sidebar-section sidebar-module-item" data-title="{{ strtolower(__($item['title'])) }}">
                    <div class="nav-item{{ $isActive ? ' expanded' : '' }}" data-menu="{{ $item['routeName'] }}">
                        @if (!empty($item['routeName']) && !$hasSub)
                            <a href="{{ $routeName($item['routeName'], $item['param'] ?? []) }}"
                                class="nav-link{{ $isActive ? ' active' : '' }} text-decoration-none" draggable="true">
                                <span class="nav-icon">{!! __($item['icon']) !!}</span>
                                <span class="nav-text">{{ __($item['title']) }}</span>
                            </a>
                        @else
                            <div class="nav-link{{ $isActive ? ' active' : '' }} text-decoration-none"
                                @if ($hasSub) onclick="toggleMenu(this)" @endif>
                                <span class="nav-icon">{!! __($item['icon']) !!}</span>
                                <span class="nav-text">{{ __($item['title']) }}</span>
                                @if ($hasSub)
                                    <i class="fas fa-chevron-down"></i>
                                @endif
                            </div>
                        @endif
                        @if ($hasSub)
                            <div class="sub-menu" style="{{ $isActive ? 'display:block;' : '' }}">
                                @foreach ($item['sub'] as $subItem)
                                    @if (auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions']))
                                        @php
                                            $isSubActive =
                                                !empty($subItem['routeName']) &&
                                                $currentRoute === $subItem['routeName'];
                                        @endphp
                                        <a href="{{ $routeName($subItem['routeName'], $subItem['param'] ?? []) }}"
                                            class="sub-nav-item sidebar-module-subitem{{ $isSubActive ? ' active' : '' }} text-decoration-none"
                                            data-title="{{ strtolower(__($subItem['title'])) }}" draggable="true">
                                            {!! __($subItem['icon']) !!}
                                            {{ __($subItem['title']) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('sidebar-search-input');
        const menuList = document.getElementById('sidebar-menu-list');
        const moduleItems = menuList.querySelectorAll('.sidebar-module-item');

        // Hàm so sánh không phân biệt hoa thường giống like %key% trong database
        function likeInsensitive(haystack, needle) {
            if (!needle) return true;
            return haystack.toLocaleLowerCase().includes(needle.toLocaleLowerCase());
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            moduleItems.forEach(function(module) {
                // Chỉ tìm theo tên mục lớn (main module)
                const mainTitle = module.getAttribute('data-title') || '';
                if (likeInsensitive(mainTitle, query)) {
                    module.style.display = '';
                } else {
                    module.style.display = 'none';
                }

                // Reset trạng thái mở rộng/collapse khi tìm kiếm
                const navItem = module.querySelector('.nav-item');
                const subMenu = module.querySelector('.sub-menu');
                if (navItem) {
                    if (likeInsensitive(mainTitle, query)) {
                        // Giữ nguyên trạng thái expanded nếu đang active, ngược lại collapse
                        if (!navItem.classList.contains('expanded')) {
                            navItem.classList.remove('expanded');
                        }
                        if (subMenu && !navItem.classList.contains('expanded')) {
                            subMenu.style.display = '';
                        }
                    } else {
                        navItem.classList.remove('expanded');
                        if (subMenu) subMenu.style.display = '';
                    }
                }
            });
        });
    });
</script>
