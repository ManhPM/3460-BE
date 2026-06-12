<link rel="stylesheet" href="{{ asset('user/assets/css/bottom-nav/bottom-nav.css') }}">
<nav class="bottom-nav">
    <div class="bottom-nav-content">
        <a href="{{ route('user.index') }}"
            class="nav-bottom-item {{ request()->routeIs('user.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Trang chủ</span>
        </a>
        <a href="{{ route('user.product.saleLimited') }}"
            class="nav-bottom-item {{ request()->routeIs('user.product.saleLimited') ? 'active' : '' }}">
            <i class="fas fa-bolt"></i>
            <span>Flash sale</span>
        </a>
        <a href="{{ route('user.cart.index') }}"
            class="nav-bottom-item {{ request()->routeIs('user.cart.index') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Giỏ hàng</span>
        </a>
        <a href="{{ route('user.cart.checkout') }}"
            class="nav-bottom-item {{ request()->routeIs('user.cart.checkout') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i>
            <span>Thanh toán</span>
        </a>
        <a href="{{ route('user.profile.indexUser') }}"
            class="nav-bottom-item {{ request()->routeIs('user.profile.indexUser') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Tài khoản</span>
        </a>
    </div>
</nav>
