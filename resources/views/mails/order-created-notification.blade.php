@php
    $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
    $settings = $settingRepository->getAll();
    $exchangePercent = (int) $settings->where('setting_key', 'exchange_percent')->first()->plain_value;
@endphp

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Xác Nhận Đơn Hàng</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 10px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .success-icon svg {
            width: 45px;
            height: 45px;
            stroke: #ffffff;
            stroke-width: 3;
            fill: none;
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        .header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .order-code {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            padding: 12px 24px;
            border-radius: 30px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Content */
        .content {
            padding: 40px 30px;
        }

        .section {
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: '';
            width: 6px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }

        /* Info Cards */
        .info-card {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #e2e8f0;
        }

        .info-row {
            display: flex;
            margin-bottom: 16px;
            align-items: flex-start;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 140px;
            font-size: 14px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .info-value {
            color: #2d3748;
            font-size: 14px;
            flex: 1;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        /* Products */
        .products-wrapper {
            background: #f7fafc;
            border-radius: 15px;
            padding: 20px;
            overflow: hidden;
        }

        .product-item {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .product-item:last-child {
            margin-bottom: 0;
        }

        .product-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .product-main {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #e2e8f0;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 15px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .product-variation {
            display: inline-block;
            background: #edf2f7;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            color: #4a5568;
            margin-top: 5px;
        }

        .product-pricing {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding-top: 15px;
            border-top: 1px dashed #e2e8f0;
        }

        .pricing-item {
            text-align: center;
        }

        .pricing-label {
            font-size: 11px;
            color: #718096;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pricing-value {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }

        .pricing-total {
            color: #667eea;
            font-size: 16px;
        }

        /* === CẢI THIỆN SUMMARY - GIÁ TIỀN ĐẸP, KHÔNG BỊ DÍNH === */
        .summary {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 15px;
            padding: 28px 25px;
            margin-top: 30px;
            font-family: inherit;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            table-layout: fixed;
        }

        .summary-table td {
            padding: 0;
            vertical-align: middle;
        }

        .summary-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            padding: 10px 0;
            font-size: 14.5px;
            align-items: center;
        }

        .summary-row .label {
            color: #4a5568;
            font-weight: 500;
            word-break: break-word;
            overflow-wrap: break-word;
            padding-right: 8px;
        }

        .summary-row .value {
            color: #2d3748;
            font-weight: 600;
            text-align: right;
            white-space: nowrap;
            font-family: monospace;
            flex-shrink: 0;
            min-width: fit-content;
        }

        .summary-row.discount .label,
        .summary-row.discount .value {
            color: #48bb78;
            font-weight: 700;
        }

        .summary-row.fee .label,
        .summary-row.fee .value {
            color: #e53e3e;
            font-weight: 700;
        }

        .summary-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #cbd5e0, transparent);
            margin: 18px 0;
        }

        .summary-total {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            padding: 22px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            margin-top: 10px;
            align-items: center;
        }

        .summary-total .label {
            color: #ffffff;
            font-weight: 700;
            font-size: 18px;
            word-break: break-word;
            overflow-wrap: break-word;
            padding-right: 8px;
        }

        .summary-total .amount {
            color: #ffffff;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 0.5px;
            text-align: right;
            font-family: monospace;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: fit-content;
        }

        /* Thank You */
        .thank-you {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #fef5e7 0%, #fdebd0 100%);
            border-radius: 15px;
            margin-top: 30px;
        }

        .thank-you p {
            color: #744210;
            font-size: 15px;
            margin: 8px 0;
            line-height: 1.6;
        }

        .thank-you p:first-child {
            font-weight: 600;
            font-size: 17px;
        }

        /* Footer */
        .footer {
            background: #2d3748;
            color: #a0aec0;
            text-align: center;
            padding: 30px;
            font-size: 13px;
        }

        .footer p {
            margin: 8px 0;
        }

        .footer .brand {
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px 5px;
            }

            .email-container {
                border-radius: 15px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .success-icon {
                width: 60px;
                height: 60px;
            }

            .success-icon svg {
                width: 35px;
                height: 35px;
            }

            .order-code {
                padding: 10px 20px;
                font-size: 14px;
            }

            .content {
                padding: 30px 20px;
            }

            .section-title {
                font-size: 16px;
            }

            .info-card {
                padding: 20px;
            }

            .info-row {
                flex-direction: column;
                margin-bottom: 12px;
                gap: 4px;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 0;
                margin-right: 0;
                font-size: 13px;
                width: 100%;
            }

            .info-value {
                font-size: 13px;
                width: 100%;
                padding-left: 0;
            }

            .products-wrapper {
                padding: 15px;
            }

            .product-item {
                padding: 15px;
            }

            .product-main {
                flex-direction: column;
                gap: 12px;
            }

            .product-image {
                width: 100%;
                height: 200px;
                border-radius: 8px;
            }

            .product-name {
                font-size: 14px;
            }

            .product-pricing {
                grid-template-columns: 1fr;
                gap: 12px;
                padding-top: 12px;
            }

            .pricing-item {
                display: flex;
                justify-content: space-between;
                text-align: left;
            }

            .pricing-label {
                font-size: 12px;
            }

            .pricing-value {
                font-size: 15px;
            }

            /* Responsive Summary */
            .summary {
                padding: 22px 18px;
                overflow-x: auto;
            }

            .summary-table {
                min-width: 100%;
                width: 100%;
            }

            .summary-row {
                gap: 12px;
                font-size: 13.5px;
                grid-template-columns: 1fr auto;
                min-width: 0;
            }

            .summary-row .label {
                padding-right: 8px;
                word-break: break-word;
                overflow-wrap: break-word;
                min-width: 0;
            }

            .summary-row .value {
                white-space: nowrap;
                flex-shrink: 0;
                min-width: fit-content;
            }

            .summary-total {
                padding: 18px 15px;
                gap: 12px;
                grid-template-columns: 1fr auto;
                min-width: 0;
            }

            .summary-total .label {
                font-size: 16px;
                padding-right: 8px;
                word-break: break-word;
                overflow-wrap: break-word;
                min-width: 0;
            }

            .summary-total .amount {
                font-size: 20px;
                white-space: nowrap;
                flex-shrink: 0;
                min-width: fit-content;
            }

            .thank-you {
                padding: 25px 20px;
            }

            .thank-you p {
                font-size: 14px;
            }

            .footer {
                padding: 25px 20px;
            }
        }

        @media only screen and (max-width: 400px) {
            .header h1 {
                font-size: 20px;
            }

            .product-image {
                height: 180px;
            }

            .summary {
                padding: 18px 12px;
            }

            .summary-row {
                gap: 8px;
                font-size: 13px;
            }

            .summary-row .label {
                font-size: 13px;
                padding-right: 6px;
            }

            .summary-row .value {
                font-size: 13px;
            }

            .summary-total {
                padding: 15px 12px;
                gap: 8px;
            }

            .summary-total .label {
                font-size: 15px;
                padding-right: 6px;
            }

            .summary-total .amount {
                font-size: 18px;
            }

            .info-card {
                padding: 15px;
            }

            .content {
                padding: 25px 15px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="success-icon">
                    <svg viewBox="0 0 52 52">
                        <path d="M14 27l9 9 19-19" stroke-dasharray="100" stroke-dashoffset="0" />
                    </svg>
                </div>
                <h1>Đặt Hàng Thành Công!</h1>
                <div class="order-code">{{ $instance->code }}</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Order Info -->
            <div class="section">
                <div class="section-title">Thông Tin Đơn Hàng</div>
                <div class="info-card">
                    <div class="info-row">
                        <span class="info-label">Ngày đặt:</span>
                        <span class="info-value">{{ format_datetime($instance->created_at) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Thanh toán:</span>
                        <span
                            class="info-value">{{ \App\Enums\Payment\PaymentMethod::getDescription($instance->payment_method) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="section">
                <div class="section-title">Thông Tin Người Nhận</div>
                <div class="info-card">
                    <div class="info-row">
                        <span class="info-label">Họ tên:</span>
                        <span class="info-value">{{ $instance->fullname }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Số điện thoại:</span>
                        <span class="info-value">{{ $instance->phone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Địa chỉ:</span>
                        <span class="info-value">{{ $instance->address }}, {{ $instance->ward->name }},
                            {{ $instance->province->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="section">
                <div class="section-title">Sản Phẩm Đã Đặt</div>
                <div class="products-wrapper">
                    @foreach ($instance->details as $item)
                        <div class="product-item">
                            <div class="product-main">
                                <img src="{{ asset($item->product_avatar) }}" alt="{{ $item->product_name }}"
                                    class="product-image">
                                <div class="product-info">
                                    <div class="product-name">{{ $item->product_name }}</div>
                                    @if ($item->product_variation_id)
                                        <div class="product-variation">
                                            @foreach ($item->productVariation->attributeVariations as $attributeVariation)
                                                {{ $attributeVariation->name }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="product-pricing">
                                <div class="pricing-item">
                                    <div class="pricing-label">Đơn giá</div>
                                    <div class="pricing-value">{{ format_price($item->unit_price) }}</div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-label">Số lượng</div>
                                    <div class="pricing-value">x{{ $item->qty }}</div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-label">Thành tiền</div>
                                    <div class="pricing-value pricing-total">
                                        {{ format_price($item->unit_price * $item->qty) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary - CẢI THIỆN HOÀN HẢO -->
            <div class="summary">
                <table class="summary-table">
                    <tr class="summary-row">
                        <td class="label">Tạm tính</td>
                        <td class="value">{{ format_price($instance->total) }}</td>
                    </tr>
                    <tr class="summary-row discount">
                        <td class="label">Giảm giá sản phẩm</td>
                        <td class="value">{{ format_price($instance->discount_value ?? 0) }}</td>
                    </tr>
                    <tr class="summary-row fee">
                        <td class="label">Phí vận chuyển</td>
                        <td class="value">{{ format_price($instance->shipping_fee ?? 0) }}</td>
                    </tr>
                    <tr class="summary-row discount">
                        <td class="label">Giảm giá vận chuyển</td>
                        <td class="value">{{ format_price($instance->voucher_shipping_discount_value ?? 0) }}</td>
                    </tr>
                    <tr class="summary-row discount">
                        <td class="label">Voucher giảm giá</td>
                        <td class="value">{{ format_price($instance->voucher_product_discount_value ?? 0) }}</td>
                    </tr>
                    <tr class="summary-row discount">
                        <td class="label">Sử dụng xu ({{ $instance->points ?? 0 }} xu)</td>
                        <td class="value">{{ format_price($instance->points * $exchangePercent ?? 0) }}</td>
                    </tr>
                </table>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <div class="label">TỔNG THANH TOÁN</div>
                    <div class="amount">
                        {{ format_price(
                            $instance->total +
                                ($instance->shipping_fee ?? 0) -
                                ($instance->discount_value ?? 0) -
                                ($instance->points * $exchangePercent ?? 0) -
                                ($instance->voucher_product_discount_value ?? 0) -
                                ($instance->voucher_shipping_discount_value ?? 0),
                        ) }}
                    </div>
                </div>
            </div>

            <!-- Thank You -->
            <div class="thank-you">
                <p>Cảm ơn bạn đã tin tưởng và đặt hàng!</p>
                <p>Đơn hàng của bạn đang được xử lý. Chúng tôi sẽ giao hàng trong thời gian sớm nhất.</p>
                <p>Mọi thắc mắc xin vui lòng liên hệ với chúng tôi.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống, vui lòng không trả lời.</p>
            <p class="brand">© 2025 LINHKA - Tất cả quyền được bảo lưu</p>
        </div>
    </div>
</body>

</html>
