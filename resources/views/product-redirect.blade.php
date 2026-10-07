<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang chuyển hướng...</title>
    <meta name="description" content="Đang mở ứng dụng LINHKA...">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="{{ url('/product/' . $productId . ($affiliateCode ? '?affiliate_code=' . $affiliateCode : '')) }}">
    <meta property="og:title" content="Sản phẩm trên LINHKA">
    <meta property="og:description" content="Xem sản phẩm này trên ứng dụng LINHKA">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url"
        content="{{ url('/product/' . $productId . ($affiliateCode ? '?affiliate_code=' . $affiliateCode : '')) }}">
    <meta property="twitter:title" content="Sản phẩm trên LINHKA">
    <meta property="twitter:description" content="Xem sản phẩm này trên ứng dụng LINHKA">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #0A2E24 0%, #03B280 50%, #00875A 100%);
            color: white;
        }

        .container {
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        h1 {
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
        }

        p {
            margin: 0.5rem 0;
            opacity: 0.9;
        }

        .button {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 12px 24px;
            background: white;
            color: #03B280;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            transition: transform 0.2s;
        }

        .button:hover {
            transform: scale(1.05);
        }
        .divider {
            margin: 1.5rem 0 1rem;
            font-size: 0.85rem;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            max-width: 80px;
        }

        .store-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .store-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .store-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .store-btn svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Đang mở ứng dụng LINHKA...</h1>
        <p id="statusMessage">Nếu ứng dụng không tự động mở, vui lòng nhấn nút bên dưới</p>
        <a href="linhka://product/{{ $productId }}{{ $affiliateCode ? '?affiliate_code=' . $affiliateCode : '' }}"
            class="button" id="openAppBtn">
            Mở trong ứng dụng LINHKA
        </a>

        <div class="divider">Chưa có ứng dụng? Tải ngay</div>

        <div class="store-buttons">
            <a href="https://play.google.com/store/apps/details?id=com.techmvv.linhka" class="store-btn" target="_blank">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.609 1.814L13.793 12 3.61 22.186a2.29 2.29 0 01-.61-.958V2.772c0-.36.216-.693.61-.958zM15.207 13.414l2.586 2.586-12.75 7.362 10.164-9.948zm2.586-5.414L15.207 10.586 5.043.638l12.75 7.362zm1.414 1.414l3.182 1.838a1.5 1.5 0 010 2.596l-3.182 1.838-2.222-2.222 2.222-2.05z"/>
                </svg>
                Google Play
            </a>
            <a href="https://apps.apple.com/app/linhka-app/id6814519184" class="store-btn" id="appStoreBtn" target="_blank">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.84c.64-.78 1.08-1.86.96-2.94-1 .04-2.15.67-2.82 1.45-.58.67-1.1 1.77-.96 2.83 1.11.09 2.19-.56 2.82-1.34z"/>
                </svg>
                App Store
            </a>
        </div>
    </div>

    <script>
        const productId = {{ $productId }};
        const affiliateCode = @json($affiliateCode);
        const deepLink = `linhka://product/${productId}${affiliateCode ? '?affiliate_code=' + encodeURIComponent(affiliateCode) : ''}`;

        const openAppBtn = document.getElementById('openAppBtn');
        if (openAppBtn) {
            openAppBtn.href = deepLink;
        }

        function tryOpenApp() {
            window.location.href = deepLink;

            setTimeout(() => {
                const status = document.getElementById('statusMessage');
                if (status) {
                    status.innerHTML = 'Nếu ứng dụng chưa cài đặt, vui lòng chọn tải từ Google Play hoặc App Store bên dưới.';
                }
            }, 2500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            tryOpenApp();
        });

        if (openAppBtn) {
            openAppBtn.addEventListener('click', (e) => {
                e.preventDefault();
                tryOpenApp();
            });
        }
    </script>
</body>
</html>
