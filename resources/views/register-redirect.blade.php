<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản LINHKA</title>
    <meta name="description" content="Đăng ký tài khoản LINHKA ngay hôm nay để nhận nhiều ưu đãi hấp dẫn!">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/register' . ($ref ? '?ref=' . $ref : '')) }}">
    <meta property="og:title" content="Tham gia cùng tôi trên LINHKA">
    <meta property="og:description" content="Đăng ký tài khoản LINHKA với mã giới thiệu {{ $ref ?? 'LINHKA' }} để nhận ngay các ưu đãi đặc quyền!">
    <meta property="og:image" content="{{ asset('public/assets/images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #03B280;
            --primary-dark: #028A63;
            --primary-light: #E6F7F2;
            --accent-orange: #FF6B00;
            --neutral-900: #191C1F;
            --neutral-600: #5F6C72;
            --neutral-100: #F2F4F5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0A2E24 0%, #03B280 50%, #00875A 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #fff;
        }

        .card {
            background: #ffffff;
            color: var(--neutral-900);
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #03B280, #FF6B00);
        }

        .logo-wrap {
            margin-bottom: 20px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 30px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--neutral-900);
            line-height: 1.3;
            margin-bottom: 8px;
        }

        p.subtitle {
            font-size: 14px;
            color: var(--neutral-600);
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .referral-box {
            background: #F8FAF9;
            border: 2px dashed #03B280;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            position: relative;
        }

        .referral-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--neutral-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .referral-code-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .referral-code {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 1px;
            user-select: all;
        }

        .btn-copy {
            background: #fff;
            border: 1px solid #D0D5DD;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--neutral-900);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-copy:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-copy.copied {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .status-spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(3, 178, 128, 0.2);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #03B280 0%, #028A63 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(3, 178, 128, 0.35);
            transition: transform 0.15s, box-shadow 0.15s;
            margin-bottom: 16px;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            font-size: 12px;
            color: #98A2B3;
            text-transform: uppercase;
            font-weight: 600;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E4E7EC;
        }

        .store-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .store-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 12px;
            background: #111;
            color: #fff;
            border-radius: 12px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .store-btn:hover {
            opacity: 0.9;
        }

        .store-btn svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 11px;
            color: #98A2B3;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="brand-badge">
            ✨ LINHKA AFFILIATE
        </div>

        <h1>Đăng ký thành viên</h1>
        <p class="subtitle">Khám phá sắm quà cùng hàng ngàn ưu đãi hấp dẫn từ hệ thống LINHKA</p>

        @if($ref)
        <div class="referral-box">
            <div class="referral-label">Mã người giới thiệu</div>
            <div class="referral-code-wrap">
                <span class="referral-code" id="refCodeText">{{ $ref }}</span>
                <button type="button" class="btn-copy" id="btnCopy" onclick="copyRefCode()">
                    Sao chép
                </button>
            </div>
        </div>
        @endif

        <div class="status-spinner" id="statusMessage">
            <div class="spinner"></div>
            <span>Đang chuyển hướng vào ứng dụng...</span>
        </div>

        <a href="linhka://register{{ $ref ? '?ref=' . urlencode($ref) : '' }}" class="btn-primary" id="btnOpenApp">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
            Mở ứng dụng LINHKA
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

        <div class="footer-note">
            Hệ sinh thái thương mại điện tử LINHKA
        </div>
    </div>

    <script>
        const ref = @json($ref);
        const deepLink = `linhka://register${ref ? '?ref=' + encodeURIComponent(ref) : ''}`;

        function copyRefCode() {
            if (!ref) return;
            navigator.clipboard.writeText(ref).then(() => {
                const btn = document.getElementById('btnCopy');
                if (btn) {
                    btn.textContent = 'Đã chép!';
                    btn.classList.add('copied');
                    setTimeout(() => {
                        btn.textContent = 'Sao chép';
                        btn.classList.remove('copied');
                    }, 2000);
                }
            }).catch(() => {
                // Fallback for older browsers
                const temp = document.createElement('input');
                temp.value = ref;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
            });
        }

        // Tự động mở App khi vào trang
        function tryOpenApp() {
            window.location.href = deepLink;

            setTimeout(() => {
                const status = document.getElementById('statusMessage');
                if (status) {
                    status.innerHTML = '<span>Nếu ứng dụng không tự mở, vui lòng nhấn nút bên dưới.</span>';
                }
            }, 2500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            tryOpenApp();
        });
    </script>
</body>

</html>
