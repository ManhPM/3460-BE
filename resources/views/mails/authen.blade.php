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
    <title>Xác thực tài khoản</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .content-padding {
                padding: 30px 20px !important;
            }

            .header-padding {
                padding: 40px 20px !important;
            }

            .code-box {
                padding: 24px 16px !important;
            }

            .code-text {
                font-size: 36px !important;
                letter-spacing: 8px !important;
            }
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%); min-height: 100vh;">
    <table width="100%" cellpadding="0" cellspacing="0" style="min-height: 100vh; padding: 40px 20px;">
        <tr>
            <td align="center" valign="middle">
                <table width="600" cellpadding="0" cellspacing="0" class="email-container"
                    style="max-width: 600px; width: 100%; background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">

                    <!-- Header với gradient -->
                    <tr>
                        <td class="header-padding"
                            style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); padding: 50px 40px; text-align: center;">
                            <img src="{{ asset($logo) }}" alt="Logo"
                                style="width: 140px; height: auto; margin-bottom: 24px; filter: brightness(0) invert(1); display: block; margin-left: auto; margin-right: auto;">
                            <h1
                                style="color: white; margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                                Xác Thực Tài Khoản</h1>
                            <p style="color: rgba(255, 255, 255, 0.9); margin: 12px 0 0 0; font-size: 16px;">Xác nhận
                                danh tính của bạn</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="content-padding" style="padding: 50px 40px;">
                            <div style="margin-bottom: 32px; text-align: center;">
                                <p style="color: #334155; font-size: 17px; line-height: 1.6; margin: 0 0 16px 0;">
                                    Xin chào <strong
                                        style="color: #6366f1; font-weight: 600;">{{ $fullname }}</strong> 👋
                                </p>
                                <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 12px 0;">
                                    Chúng tôi vừa nhận được yêu cầu xác thực cho tài khoản:
                                </p>
                                <p
                                    style="color: #6366f1; font-size: 15px; line-height: 1.6; margin: 0; font-weight: 600; word-break: break-word;">
                                    📧 {{ $email }}
                                </p>
                            </div>

                            <p
                                style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 28px 0; text-align: center;">
                                Vui lòng nhập mã xác thực bên dưới để hoàn tất quá trình xác thực:
                            </p>

                            <!-- Verification Code Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                <tr>
                                    <td class="code-box"
                                        style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px dashed #6366f1; border-radius: 16px; padding: 36px; text-align: center;">
                                        <p
                                            style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 16px 0; font-weight: 600;">
                                            Mã Xác Thực</p>
                                        <div class="code-text"
                                            style="font-size: 48px; font-weight: 800; color: #6366f1; letter-spacing: 12px; margin: 0; font-family: 'Courier New', monospace;">
                                            {{ $verify_code }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                <tr>
                                    <td
                                        style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; border-radius: 12px; padding: 20px 24px;">
                                        <p
                                            style="color: #92400e; font-size: 14px; line-height: 1.6; margin: 0; text-align: center;">
                                            <strong style="display: block; margin-bottom: 6px;">⚠️ Lưu ý quan
                                                trọng:</strong>
                                            Mã này có hiệu lực trong <strong>30 phút</strong>. Không chia sẻ mã với bất
                                            kỳ ai để bảo vệ tài khoản của bạn.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Thank you message -->
                            <div
                                style="text-align: center; margin-top: 40px; padding-top: 32px; border-top: 1px solid #e2e8f0;">
                                <p style="color: #64748b; font-size: 15px; margin: 0 0 8px 0;">
                                    Nếu bạn không yêu cầu xác thực này, vui lòng bỏ qua email hoặc liên hệ với chúng tôi
                                    ngay.
                                </p>
                                <p style="color: #334155; font-size: 16px; font-weight: 600; margin: 16px 0 0 0;">
                                    Trân trọng,<br>
                                    <span style="color: #6366f1;">Mevivu Team</span>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Bottom decorative strip -->
                    <tr>
                        <td
                            style="background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%); height: 8px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
