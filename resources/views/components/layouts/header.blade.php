<link rel="stylesheet" href="{{ asset('user/assets/css/header/header.css') }}">
@php
    $currentUser = auth('web')->user();
@endphp
<div id="top-bar" class="pb-1 pt-1 shadow-sm">
    <div class="container p-0">
        <p class="fs-12 m-0">
            <span
                class="text-default fw-bold">{{ $settings->where('setting_key', 'topbar_title')->first()->plain_value }}</span>
        </p>
    </div>
</div>
<div id="top-header" class="d-flex align-items-center justify-content-center wrap-nav">
    <div class="container">
        <div class="row pb-2 pt-2">
            <!-- Logo -->
            <div class="col-3 d-flex align-items-center">
                <x-link :href="route('user.index')">
                    <img style="max-height: 80px" class="img-fluid"
                        src="{{ asset($settings->where('setting_key', 'site_logo')->first()->plain_value) }}"
                        alt="Mevivu">
                </x-link>
            </div>
            <!-- Search Bar -->
            <div class="col-6 d-flex justify-content-center align-items-center dropdown">
                <div class="input-group dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-target="#menu-1">
                    <input type="text" class="form-control" id="search-input"
                        placeholder="Nhập từ khóa bạn muốn tìm kiếm..." aria-label="Text input with dropdown button">
                    <x-button id="search-button" type="button" class="bg-default"><i
                            class="ti ti-search fs-4 text-white"></i></x-button>
                </div>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" id="menu-pc">
                    <li>
                        <a href="#" class="dropdown-item">
                            <i class="ti ti-search"></i>
                            Nhập từ khóa tìm kiếm...
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Cart Icon -->
            <div class="col-3 d-flex justify-content-center align-items-center">
                @if (!$currentUser)
                    <div class="pe- me-5"><a class="top-item text-black" href="{{ route('user.auth.indexUser') }}">
                            Tài khoản</a></div>
                @else
                    <div class="position-relative me-5">
                        <a class="top-item d-flex align-items-center text-black"
                            href="{{ route('user.profile.indexUser') }}">
                            <img src="{{ asset($currentUser->avatar ?? 'assets/images/avatar-user.png') }}"
                                alt="Avatar" class="rounded-circle me-2" width="40" height="40">
                        </a>
                        <div class="dropdown-menu position-absolute start-50 translate-middle-x" id="userDropdown">
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.profile.indexUser') }}">
                                <i class="fa fa-user me-2" style="width: 20px; text-align: center;"></i> Tài khoản
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.contact-user.index') }}">
                                <i class="fa fa-mail-bulk me-2" style="width: 20px; text-align: center;"></i> Liên hệ
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.wishlist.index') }}">
                                <i class="fa fa-heart me-2" style="width: 20px; text-align: center;"></i> Yêu thích
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.membership_level.index') }}">
                                <i class="fa fa-gem me-2" style="width: 20px; text-align: center;"></i> Thành viên
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.notification.index') }}">
                                <i class="fa fa-bell me-2" style="width: 20px; text-align: center;"></i> Thông báo
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.order.indexUser') }}">
                                <i class="fa fa-receipt me-2" style="width: 20px; text-align: center;"></i> Đơn hàng
                            </a>
                            @if (env('IS_PRO'))
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('user.order.affiliate') }}">
                                    <i class="fa fa-dollar-sign me-2" style="width: 20px; text-align: center;"></i> Hoa
                                    hồng
                                    AFF
                                </a>
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('user.commission_withdrawal.indexUser') }}">
                                    <i class="fa fa-credit-card me-2" style="width: 20px; text-align: center;"></i> Giao
                                    dịch AFF
                                </a>
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('user.voucher.index') }}">
                                    <i class="fa fa-ticket me-2" style="width: 20px; text-align: center;"></i> Voucher
                                </a>
                            @endif
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.address.index') }}">
                                <i class="fa-solid fa-location-dot me-2" style="width: 20px; text-align: center;"></i>
                                Địa chỉ
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.order.statistical') }}">
                                <i class="fa fa-chart-area me-2" style="width: 20px; text-align: center;"></i> Thống kê
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.password.indexUser') }}">
                                <i class="fa fa-key me-2" style="width: 20px; text-align: center;"></i> Mật khẩu
                            </a>
                            <a id="showModal" href="{{ route('user.logout') }}"
                                class="dropdown-item d-flex align-items-center" data-bs-target="#modalLogout">
                                <i class="fa fa-sign-out me-2" style="width: 20px; text-align: center;"></i>
                                {{ __('Đăng xuất') }}
                            </a>
                        </div>
                    </div>
                @endif
                @if ($currentUser)

                    @php
                        $isNoNewNotification = false;
                        $user = $currentUser;
                        $notifications = $user->unreadNotifications()->take(5)->get();
                        if ($notifications->isEmpty()) {
                            $isNoNewNotification = true;
                            $notifications = $user->notifications()->latest()->take(5)->get();
                        }
                    @endphp

                    @push('custom-js')
                        <script>
                            $(document).ready(function() {
                                let dropdownTimeout;

                                $(".dropdown-notification").on("mouseenter", function() {
                                    clearTimeout(dropdownTimeout); // Xóa timeout để tránh bị ẩn khi hover vào lại
                                    $(this).find(".dropdown-menu-notification").stop(true, true).fadeIn(300);
                                });

                                $(".dropdown-notification, .dropdown-menu-notification").on("mouseleave", function() {
                                    dropdownTimeout = setTimeout(() => {
                                        $(".dropdown-menu-notification").fadeOut(300);
                                    }, 500); // Delay 500ms trước khi ẩn để tránh bị mất ngay khi di chuột qua lại
                                });

                                $(".dropdown-menu-notification").on("mouseenter", function() {
                                    clearTimeout(dropdownTimeout); // Khi chuột vào dropdown, không ẩn
                                });
                            });
                        </script>
                    @endpush
                    <div class="position-relative dropdown-notification me-3">
                        <i style="font-size: 1.5em; cursor: pointer;" class="fa fa-bell" id="notificationIcon"></i>
                        <span id="notification-count"
                            class="position-absolute start-100 translate-middle badge rounded-pill bg-danger top-0"
                            style="left: 100% !important;">
                            {{ $notifications ? ($isNoNewNotification ? 0 : $notifications->count()) : 0 }}
                        </span>
                        <ul class="dropdown-menu-notification position-absolute rounded bg-white p-2 shadow"
                            style="width: 300px; display: none; right: 0; top: 120%;">
                            <li class="dropdown-header fw-bold border-bottom py-2 text-center">Thông báo mới nhận</li>
                            @foreach ($notifications as $notification)
                                <li>
                                    <a class="dropdown-item d-flex flex-column text-dark text-decoration-none p-2"
                                        href="{{ route('user.notification.show', ['id' => $notification->id]) }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong style="font-size: 0.85em;">{{ $notification->title }}</strong>
                                            @if (is_null($notification->read_at))
                                                <span class="badge bg-danger ms-2" style="font-size: 0.7em;">Chưa
                                                    đọc</span>
                                            @else
                                                <span class="badge bg-success ms-2" style="font-size: 0.7em;">Đã
                                                    đọc</span>
                                            @endif
                                        </div>
                                        <small
                                            class="text-muted d-block mt-1">{{ $notification->created_at->diffForHumans() }}</small>
                                    </a>
                                </li>
                                <hr class="my-1">
                            @endforeach
                            <li class="text-center">
                                <a href="{{ route('user.notification.index') }}"
                                    class="text-dark text-decoration-none">Xem
                                    tất cả</a>
                            </li>
                        </ul>
                    </div>
                @endif

                <div id="cartButton" class="position-relative">
                    @if (Route::is('user.cart.checkout'))
                        <i onclick="location.href='{{ route('user.cart.index') }}';"
                            style="font-size: 1.5em;cursor: pointer;" class="fa fa-shopping-cart"></i>
                    @else
                        <i style="font-size: 1.5em;cursor: pointer;" class="fa fa-shopping-cart"></i>
                    @endif
                    <span id="cart-count"
                        class="position-absolute start-100 translate-middle badge rounded-pill bg-danger top-0"
                        style="left: 100% !important;">
                        @if ($currentUser)
                            {{ $currentUser->shopping_cart()->sum('qty') }}
                        @else
                            @php
                                $cart = session()->get('cart', []);
                                $totalQuantity = 0;
                                foreach ($cart as $item) {
                                    $totalQuantity += $item['qty'];
                                }
                            @endphp
                            {{ $totalQuantity }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navbar -->
<div id="navbar" class="">
    <div class="d-flex align-items-center justify-content-center wrap-nav bg-nav-responsive">
        <div class="row p-m-0 container">
            <!-- Categories Menu -->
            <div style="cursor: pointer; margin-left: -16px" onclick="showTabContent()"
                class="col-3 d-flex align-items-center widget d-none d-xl-flex">
                <h6 class="d-flex align-items-center m-0 text-start" style="height: 45px;">
                    <i class="ti ti-list px-2"></i>Tất cả danh mục
                </h6><br>
                <ul id="menu-1-TQYyg" class="nav-menu"></ul>
            </div>

            <!-- Main Navigation Links -->
            <div class="col-9 d-flex align-items-center d-none d-xl-flex bold-text">
                <ul class="nav">
                    <li class="nav-item default-font-size">
                        <x-link :href="route('user.index')">Trang chủ</x-link>
                    </li>
                    <li class="nav-item default-font-size">
                        <x-link :href="route('user.information')">Giới thiệu</x-link>
                    </li>
                    <li class="nav-item default-font-size">
                        <x-link :href="route('user.product.indexUser')">Sản phẩm</x-link>
                    </li>
                    <li class="nav-item default-font-size">
                        <x-link :href="route('user.contact')">Liên hệ</x-link>
                    </li>
                    @if (isset($flashSale))
                        <li class="nav-item default-font-size">
                            <x-link :href="route('user.product.saleLimited')">Flash Sale</x-link>
                        </li>
                    @endif
                    <li class="nav-item default-font-size">
                        <x-link :href="route('user.post.index')">Tin tức</x-link>
                    </li>
                    @if (env('IS_PRO'))
                        <li class="nav-item default-font-size">
                            <x-link :href="$currentUser
                                ? route('user.voucher_program.index')
                                : route('user.auth.indexUser')">
                                Voucher</x-link>
                        </li>
                    @endif
                    <li class="nav-item default-font-size">
                        <x-link :href="route('user.getOrderDetailForCustomer')">Tra cứu đơn hàng</x-link>
                    </li>
                </ul>
            </div>
            <!-- NavBar Responsive-->
            <div class="nav-responsive row d-xl-none d-flex container">
                <button class="col-3 btn d-xl-none d-block text-start" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    <i class="fa fa-bars default-double-font-size"></i>
                </button>
                <div class="col-6 d-flex justify-content-center align-items-center">
                    <x-link :href="route('user.index')">
                        <img style="max-height: 40px"
                            src="{{ asset($settings->where('setting_key', 'site_logo')->first()->plain_value) }}"
                            alt="Mevivu" class="img-fluid">
                    </x-link>
                </div>
                <div class="col-3 d-flex justify-content-center align-items-center cart ps-1">
                    <!-- Cart Icon -->
                    <div class="position-relative shoppingCartIcon me-3">
                        <i onclick="location.href='{{ route('user.cart.index') }}';"
                            style="font-size: 1.8em; cursor: pointer;" class="fa fa-shopping-cart ms-5"></i>
                        <span id="cart-count-mobile"
                            class="position-absolute start-100 translate-middle badge rounded-pill bg-danger top-0"
                            style="left: 100% !important;">
                            @if ($currentUser)
                                {{ $currentUser->shopping_cart()->sum('qty') }}
                            @else
                                @php
                                    $cart = session()->get('cart', []);
                                    $totalQuantity = 0;
                                    foreach ($cart as $item) {
                                        $totalQuantity += $item['qty'];
                                    }
                                @endphp
                                {{ $totalQuantity }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="search-container d-xl-none">
                <form id="searchForm" class="search-form">
                    <input type="text" class="search-input" placeholder="Nhập từ khóa tìm kiếm..."
                        id="search-input-mobile">
                    <button type="button" id="search-button-mobile" class="search-button-mobile">
                        <i class="fa fa-search"></i>
                    </button>
                    <!-- Nút X để xóa text tìm kiếm -->
                    <button type="button" id="clear-search" class="clear-search-button" style="display: none;">
                        <i class="fa fa-times"></i> <!-- Biểu tượng nút X -->
                    </button>
                </form>
                <ul id="search-results" class="list-unstyled"></ul> <!-- Thẻ để chứa kết quả tìm kiếm -->
            </div>
            <!-- Offcanvas Menu -->
            <div class="offcanvas offcanvas-start d-xl-none d-block" tabindex="-1" id="offcanvasExample"
                aria-labelledby="offcanvasExampleLabel">
                <div class="offcanvas-header">
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fs-6 bold-text text-black" id="menu-tab"
                                data-bs-toggle="tab" data-bs-target="#menu" type="button" role="tab"
                                aria-controls="menu" aria-selected="true">
                                <i class="ti ti-list"></i> Menu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fs-6 bold-text text-black" id="category-tab" data-bs-toggle="tab"
                                data-bs-target="#category" type="button" role="tab" aria-controls="category"
                                aria-selected="false">
                                <i class="ti ti-list"></i> Danh mục
                            </button>
                        </li>
                    </ul>
                    <div style="max-height: 600px; overflow-y: auto;" class="tab-content" id="myTabContent">
                        <div style="height: auto" class="tab-pane fade show active" id="menu" role="tabpanel"
                            aria-labelledby="menu-tab">
                            <ul style="margin-top: 0px" class="nav">
                                <li class="nav-item nav-item-menu">
                                    <x-link :href="route('user.index')">Trang chủ</x-link>
                                </li>
                                <li class="nav-item nav-item-menu">
                                    <x-link :href="route('user.information')">Giới thiệu</x-link>
                                </li>
                                <li class="nav-item nav-item-menu">
                                    <x-link :href="route('user.product.indexUser')">Sản phẩm</x-link>
                                </li>
                                <li class="nav-item nav-item-menu">
                                    <x-link :href="route('user.contact')">Liên hệ</x-link>
                                </li>
                                @if (isset($flashSale))
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.product.saleLimited')">Flash Sale</x-link>
                                    </li>
                                @endif
                                <li class="nav-item nav-item-menu">
                                    <x-link :href="route('user.post.index')">Tin tức</x-link>
                                </li>

                                @if (env('IS_PRO'))
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="$currentUser
                                            ? route('user.voucher_program.index')
                                            : route('user.auth.indexUser')">Voucher</x-link>
                                    </li>
                                @endif

                                @if (!$currentUser)
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.auth.indexUser')">Đăng nhập / Đăng ký</x-link>
                                    </li>
                                @endif

                                @if ($currentUser)
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.order.indexUser')">Đơn hàng</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.profile.indexUser')">Tài khoản</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.wishlist.index')">Yêu thích</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.membership_level.index')">Thành viên</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.notification.index')">Thông báo</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.address.index')">Địa chỉ</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.order.statistical')">Thống kê</x-link>
                                    </li>
                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.password.indexUser')">Mật khẩu</x-link>
                                    </li>

                                    @if (env('IS_PRO'))
                                        <li class="nav-item nav-item-menu">
                                            <x-link :href="route('user.order.affiliate')">Hoa hồng AFF</x-link>
                                        </li>
                                        <li class="nav-item nav-item-menu">
                                            <x-link :href="route('user.commission_withdrawal.indexUser')">Giao dịch AFF</x-link>
                                        </li>
                                    @endif

                                    <li class="nav-item nav-item-menu">
                                        <x-link :href="route('user.logout')">Đăng xuất</x-link>
                                    </li>
                                @endif
                            </ul>

                        </div>
                        <div style="height: auto" class="tab-pane fade" id="category" role="tabpanel"
                            aria-labelledby="category-tab">
                            <div class="menu-container">
                                <div class="menu-slide">
                                    <div class="menu-panel" id="main-menu">
                                        <ul class="menu">
                                            @foreach ($parentCategories as $category)
                                                <li class="menu-item">
                                                    <a
                                                        href="{{ route('user.product.indexUser', ['category_ids[]' => $category->slug]) }}">
                                                        <i class="{{ $category->icon }}"></i>
                                                        {{ $category->name }}
                                                    </a>
                                                    @if (isset($category->children[0]))
                                                        <i class="ti ti-chevron-right"
                                                            onclick="showSubMenu('{{ $category->id }}', '{{ $category->name }}')"></i>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="menu-panel" id="submenu">
                                        <p class="back-button" onclick="showMainMenu()">
                                            <i class="ti ti-chevron-left"></i>
                                        </p>
                                        <h3 id="submenu-title" class="mb-3 text-center"></h3>
                                        <ul class="menu" id="submenu-list"></ul>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input-mobile');
        const clearButton = document.getElementById('clear-search');

        // Khi người dùng bắt đầu gõ, hiển thị nút X
        searchInput.addEventListener('input', function() {
            if (searchInput.value.length > 0) {
                clearButton.style.display = 'block'; // Hiển thị nút X
            } else {
                clearButton.style.display = 'none'; // Ẩn nút X nếu không có nội dung
            }
        });

        // Khi người dùng nhấn nút X, xóa nội dung và ẩn nút X
        clearButton.addEventListener('click', function() {
            searchInput.value = ''; // Xóa nội dung ô tìm kiếm
            clearButton.style.display = 'none'; // Ẩn nút X
            menu = $('#search-results');
            menu.html('');
        });
    });
</script>

<script>
    const menuSlide = document.querySelector('.menu-slide');
    const menuPanels = document.querySelectorAll('.menu-panel');
    let currentPanel = 0;

    const categories = {!! json_encode($parentCategories) !!};

    function showMainMenu() {
        currentPanel = 0;
        updateMenuPosition();
    }

    function showSubMenu(categoryId, categoryName) {
        currentPanel = 1;

        document.getElementById('submenu-title').textContent = categoryName;

        const submenuList = document.getElementById('submenu-list');
        submenuList.innerHTML = '';

        const currentCategory = categories.find(cat => cat.id == categoryId);

        // Hiển thị danh mục con
        if (currentCategory && currentCategory.children) {
            displaySubcategories(currentCategory.children, submenuList);
        }

        updateMenuPosition();
    }

    function displaySubcategories(subcategories, parentElement, level = 0) {
        subcategories.forEach(subcategory => {
            const li = document.createElement('li');
            li.className = `menu-item ${level === 0 ? 'parent-category' : 'child-category'}`;
            li.style.paddingLeft = `${level * 20}px`;

            const a = document.createElement('a');
            a.href = `{{ route('user.product.indexUser') }}?category_ids[]=${subcategory.slug}`;
            if (level === 0) {
                a.innerHTML = `<i class="${subcategory.icon}"></i><strong>${subcategory.name}</strong>`;
            } else {
                a.innerHTML = `<i class="${subcategory.icon}"></i>${subcategory.name}`;
            }

            li.appendChild(a);
            parentElement.appendChild(li);

            // Đệ quy hiển thị danh mục con
            if (subcategory.children && subcategory.children.length > 0) {
                displaySubcategories(subcategory.children, parentElement, level + 1);
            }
        });
    }

    function updateMenuPosition() {
        menuSlide.style.transform = `translateX(-${currentPanel * 100}%)`;
    }
</script>
@if (Route::is('user.index'))
    <style>
        .container-vertical-sidebar {
            width: 32px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            left: 0;
            /* Thay đổi left thành 0 để dính sát mép trái */
            top: 50%;
            transform: translateY(-50%);
            border-radius: 0 4px 4px 0;
            /* Chỉ bo góc bên phải */
            z-index: 999;
        }

        .nav-item-vertical-sidebar {
            width: 100%;
            height: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #757575;
            position: relative;
            /* Thêm position relative để làm mốc cho tooltip */
        }

        .nav-item-vertical-sidebar:hover {
            color: #f57224;
        }

        .nav-item-vertical-sidebar.active {
            color: #f57224;
        }

        .icon-vertical-sidebar {
            font-size: 20px;
        }

        .text-vertical-sidebar {
            display: none;
            /* Ẩn text mặc định */
            position: absolute;
            left: 100%;
            /* Hiện bên phải của icon */
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            margin-left: 10px;
        }

        /* Tạo mũi tên cho tooltip */
        .text-vertical-sidebar::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            border-right: 4px solid rgba(0, 0, 0, 0.8);
        }

        /* Hiện tooltip khi hover */
        .nav-item-vertical-sidebar:hover .text-vertical-sidebar {
            display: block;
        }

        #targetTopBar {
            display: none;
            /* Ẩn mặc định */
        }
    </style>

    <div class="container-vertical-sidebar">
        <a id="targetTopBar" href="#" class="nav-item-vertical-sidebar">
            <i style="margin-left: 6px" class="fas fa-chevron-up icon-vertical-sidebar"></i>
            <span class="text-vertical-sidebar">Top</span>
        </a>
        <a href="#targetCategory" class="nav-item-vertical-sidebar" data-section="targetCategory">
            <i class="fas fa-th icon-vertical-sidebar"></i>
            <span class="text-vertical-sidebar">Danh mục</span>
        </a>
        <a href="#targetFlashSale" class="nav-item-vertical-sidebar" data-section="targetFlashSale">
            <i class="fas fa-fire icon-vertical-sidebar"></i>
            <span class="text-vertical-sidebar">Flash sale</span>
        </a>
        <a href="#targetMenu" class="nav-item-vertical-sidebar" data-section="targetMenu">
            <i class="fas fa-cube icon-vertical-sidebar"></i>
            <span class="text-vertical-sidebar">Menu</span>
        </a>
        <a href="#targetForYou" class="nav-item-vertical-sidebar" data-section="targetForYou">
            <i class="fas fa-user icon-vertical-sidebar"></i>
            <span class="text-vertical-sidebar">Dành cho bạn</span>
        </a>
    </div>

    <script>
        const topBar = document.getElementById('targetTopBar');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) { // Khi cuộn xuống 100px
                topBar.style.display = 'block'; // Hiển thị top-bar
            } else {
                topBar.style.display = 'none'; // Ẩn khi cuộn lên trên
            }
        });

        // Sự kiện click để cuộn lên đầu trang
        topBar.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // Cuộn mượt
            });
        });

        // Lấy tất cả các nav items
        const navItems = document.querySelectorAll('.nav-item-vertical-sidebar');

        // Xử lý click event
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault(); // Ngăn chặn hành vi mặc định của thẻ a

                // Xóa active class từ tất cả items
                navItems.forEach(i => i.classList.remove('active'));
                // Thêm active class vào item được click
                item.classList.add('active');

                // Lấy target section từ href
                const targetId = item.getAttribute('href').slice(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    // Scroll đến vị trí của section
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Xử lý scroll để active đúng section
        window.addEventListener('scroll', () => {
            // Định nghĩa các section ID mới
            const sections = [
                'targetCategory',
                'targetFlashSale',
                'targetMenu',
                'targetForYou'
            ];

            // Xóa active từ tất cả items trước
            navItems.forEach(item => item.classList.remove('active'));

            // Kiểm tra từng section
            sections.forEach(sectionId => {
                const element = document.getElementById(sectionId);
                if (element) {
                    const rect = element.getBoundingClientRect();
                    // Chỉ active khi section thực sự nằm trong viewport
                    if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
                        const targetItem = document.querySelector(`a[href="#${sectionId}"]`);
                        if (targetItem) {
                            targetItem.classList.add('active');
                        }
                    }
                }
            });
        });

        // Xóa phần set active mặc định khi load trang
        document.addEventListener('DOMContentLoaded', () => {
            // Chỉ xử lý active nếu có hash trong URL
            const hash = window.location.hash.slice(1);
            if (hash) {
                const activeItem = document.querySelector(`a[href="#${hash}"]`);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
            }
            // Không set active mặc định nếu không có hash
        });
    </script>
@endif
