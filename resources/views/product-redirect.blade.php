<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang chuyển hướng...</title>
    <meta name="description" content="Đang mở ứng dụng HC Mart...">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="{{ url('/product/' . $productId . ($affiliateCode ? '?affiliate_code=' . $affiliateCode : '')) }}">
    <meta property="og:title" content="Sản phẩm trên HC Mart">
    <meta property="og:description" content="Xem sản phẩm này trên ứng dụng HC Mart">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url"
        content="{{ url('/product/' . $productId . ($affiliateCode ? '?affiliate_code=' . $affiliateCode : '')) }}">
    <meta property="twitter:title" content="Sản phẩm trên HC Mart">
    <meta property="twitter:description" content="Xem sản phẩm này trên ứng dụng HC Mart">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .button:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Đang mở ứng dụng...</h1>
        <p>Nếu ứng dụng không tự động mở, vui lòng nhấn nút bên dưới</p>
        <a href="hcmart://product/{{ $productId }}{{ $affiliateCode ? '?affiliate_code=' . $affiliateCode : '' }}"
            class="button" id="openAppBtn">
            Mở trong ứng dụng
        </a>
    </div>

    <script>
        // Extract product ID and affiliate code from URL
        const urlParams = new URLSearchParams(window.location.search);
        const pathParts = window.location.pathname.split('/');
        const productId = {{ $productId }};
        const affiliateCode = @json($affiliateCode);

        // Build deep link
        const deepLink = `hcmart://product/${productId}${affiliateCode ? '?affiliate_code=' + affiliateCode : ''}`;

        // Update button href
        const openAppBtn = document.getElementById('openAppBtn');
        if (openAppBtn) {
            openAppBtn.href = deepLink;
        }

        // Try to open app immediately
        function tryOpenApp() {
            // Try opening the app
            window.location.href = deepLink;

            // If app doesn't open within 2 seconds, show fallback
            setTimeout(() => {
                // Check if we're still on this page (app didn't open)
                if (document.hasFocus()) {
                    // Optionally redirect to app store
                    // window.location.href = 'https://play.google.com/store/apps/details?id=com.techmvv.hcmart';
                }
            }, 2000);
        }

        // Try to open app on page load
        tryOpenApp();

        // Also try when button is clicked
        if (openAppBtn) {
            openAppBtn.addEventListener('click', (e) => {
                e.preventDefault();
                tryOpenApp();
            });
        }
    </script>
</body>

</html>
