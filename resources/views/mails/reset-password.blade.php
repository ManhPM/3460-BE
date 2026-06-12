@php
    $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
    $logo = $settingRepository->findByField('setting_key', 'site_logo')->plain_value;
@endphp
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Khôi phục mật khẩu</title>
    <style>
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 160px;
            height: auto;
            margin-bottom: 20px;
        }

        .title {
            color: #1a73e8;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .content {
            color: #5f6368;
            line-height: 1.6;
        }

        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .reset-button {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4A90E2;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #357ABD;
        }

        .warning {
            font-size: 13px;
            color: #666;
            background-color: #fff8e1;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #ffc107;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body
    style="margin: 0;
																padding: 20px;
																font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
																background-color: #f8f9fa;
																min-height: 100vh;
																background-image: url('{{ asset('assets/images/bg-mail-authen.jpg') }}');
																background-size: cover;
																background-position: center;
																display: flex;
																align-items: center;
																justify-content: center;
																box-sizing: border-box;">
    <div class="email-container">
        <div class="header">
            <img src="{{ asset($logo) }}" alt="Company Logo" class="logo">
            <h1 class="title">Khôi phục mật khẩu</h1>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $fullname }}</strong>,</p>

            <div class="user-info">
                <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn:</p>
                <p><strong>Email:</strong> {{ $email }}</p>
            </div>

            <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này. Ngược lại, bạn có thể đặt lại mật
                khẩu của
                mình bằng cách nhấp vào nút bên dưới:</p>
        </div>

        <div class="reset-button">
            <a href="{{ $url }}" class="button">Đặt lại mật khẩu</a>
        </div>

        <div class="warning">
            <p><strong>Lưu ý:</strong> Link đặt lại mật khẩu này sẽ hết hạn trong vòng 30 phút. Vì lý do bảo mật,
                vui lòng
                không chia sẻ email này với bất kỳ ai.</p>
        </div>

        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>© 2025 Mevivu. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>

</html>
