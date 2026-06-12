<!-- Thêm CSS cho Toastify -->
<link rel="stylesheet" type="text/css" href="{{ asset('libs/toastify/toastify.min.css') }}">

<!-- Thêm JavaScript cho Toastify -->
<script type="text/javascript" src="{{ asset('libs/toastify/toastify.min.js') }}"></script>


<!-- CSS tùy chỉnh cho Toastify -->
<style>
    /* Custom styles cho Toastify */
    .toastify {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .toastify-custom {
        animation: slideInRight 0.3s ease-out !important;
    }

    .toastify-error {
        animation: shake 0.5s ease-in-out !important;
    }

    .toastify-validation-error {
        animation: slideInRight 0.3s ease-out !important;
        margin-top: 8px !important;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-5px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(5px);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .toastify {
            width: calc(100% - 32px) !important;
            margin: 0 16px !important;
        }
    }
</style>

<script>
    $(document).ready(function() {

        // Function để lấy màu sắc theo loại thông báo
        function getToastifyColor(type) {
            const colors = {
                // Slightly desaturated/darker versions
                'success': 'linear-gradient(to right, #009688, #7cb342)', // Original: #00b09b, #96c93d
                'error': 'linear-gradient(to right, #d32f2f, #e53935)', // Original: #ff416c, #ff4b2b
                'warning': 'linear-gradient(to right, #e65100, #ff8f00)', // Original: #f093fb, #f5576c
                'info': 'linear-gradient(to right, #2196f3, #03a9f4)', // Original: #4facfe, #00f2fe
                'primary': 'linear-gradient(to right, #455a64, #546e7a)', // Original: #667eea, #764ba2 (changed to a muted blue/grey)
                'secondary': 'linear-gradient(to right, #bbbbbb, #9e9e9e)', // Original: #f2f2f2, #d4d4d4
                'danger': 'linear-gradient(to right, #d32f2f, #e53935)', // Same as error for consistency
                'dark': 'linear-gradient(to right, #333333, #000000)'
            };
            return colors[type] || colors['info'];
        }

        // Function để lấy icon theo loại
        function getToastifyIcon(type) {
            const icons = {
                'success': '✅',
                'error': '❌',
                'warning': '⚠️',
                'info': '💡',
                'primary': '🔵',
                'secondary': '⚪',
                'danger': '🚫',
                'dark': '⚫'
            };
            return icons[type] || '📢';
        }

        // Function để lấy position của Toastify
        function getToastifyGravity(position) {
            if (position.includes('top')) return 'top';
            if (position.includes('bottom')) return 'bottom';
            return 'top';
        }

        function getToastifyPosition(position) {
            if (position.includes('left')) return 'left';
            if (position.includes('right')) return 'right';
            if (position.includes('center')) return 'center';
            return 'right';
        }

        // Hiển thị thông báo session
        @if (isset($type))
            @foreach ($type as $value)
                @if ($message = Session::get($value))
                    Toastify({
                        text: '✅ Thông báo: ' + '{{ $message }}',
                        duration: 10000,
                        gravity: getToastifyGravity('top-right'),
                        position: getToastifyPosition('top-right'),
                        backgroundColor: getToastifyColor('{{ $value }}'),
                        className: "toastify-custom",
                        stopOnFocus: true,
                        style: {
                            borderRadius: "12px",
                            fontSize: "14px",
                            fontWeight: "500",
                            boxShadow: "0 8px 32px rgba(0, 0, 0, 0.12)",
                            padding: "16px 20px"
                        },
                        onClick: function() {
                            // Có thể thêm action khi click vào toast
                        }
                    }).showToast();
                @endif
            @endforeach
        @endif

        // Hiển thị lỗi validation
        @if (isset($errors) && $errors->any())
            Toastify({
                text: "⚠️ {{ $title ?? 'Lỗi' }}: {{ $errors->first() }}",
                duration: 10000,
                gravity: getToastifyGravity('top-right'),
                position: getToastifyPosition('top-right'),
                backgroundColor: "linear-gradient(to right, #ef5350, #e57373)", // Slightly darker/less vibrant red
                className: "toastify-error",
                stopOnFocus: true,
                style: {
                    borderRadius: "12px",
                    fontSize: "14px",
                    fontWeight: "500",
                    boxShadow: "0 8px 32px rgba(255, 0, 0, 0.12)",
                    padding: "16px 20px"
                }
            }).showToast();
        @endif

        // Hiển thị tất cả lỗi (nếu muốn)
        @if (isset($errors) && $errors->any() && $errors->count() > 1)
            @foreach ($errors->all() as $error)
                setTimeout(function() {
                    Toastify({
                        text: "❌ {{ $error }}",
                        duration: 8000,
                        gravity: getToastifyGravity($position ?? 'top-right'),
                        position: getToastifyPosition($position ?? 'top-right'),
                        backgroundColor: "linear-gradient(to right, #ef5350, #e57373)", // Consistent with single error
                        className: "toastify-validation-error",
                        stopOnFocus: true,
                        style: {
                            borderRadius: "12px",
                            fontSize: "13px",
                            fontWeight: "400",
                            boxShadow: "0 6px 24px rgba(255, 0, 0, 0.1)",
                            padding: "14px 18px"
                        }
                    }).showToast();
                }, {{ $loop->index * 500 }});
            @endforeach
        @endif
    });

    // Helper functions để sử dụng trong JavaScript
    window.showToastify = function(type, title, message, duration = 5000) {
        const colors = {
            'success': 'linear-gradient(to right, #009688, #7cb342)',
            'error': 'linear-gradient(to right, #d32f2f, #e53935)',
            'warning': 'linear-gradient(to right, #e65100, #ff8f00)',
            'info': 'linear-gradient(to right, #2196f3, #03a9f4)'
        };

        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': '💡'
        };

        Toastify({
            text: `${icons[type] || '📢'} ${title}: ${message}`,
            duration: duration,
            gravity: "top",
            position: "right",
            backgroundColor: colors[type] || colors['info'],
            className: `toastify-${type}`,
            stopOnFocus: true,
            style: {
                borderRadius: "12px",
                fontSize: "14px",
                fontWeight: "500",
                boxShadow: "0 8px 32px rgba(0, 0, 0, 0.12)",
                padding: "16px 20px"
            }
        }).showToast();
    };
</script>
