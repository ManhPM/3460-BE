<?php

return [
    'contact_user' => [
        'order' => [
            'title' => 'Mã đơn hàng',
            'icon' => 'ti-box',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'type' => [
            'title' => 'Loại yêu cầu',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Thời gian tạo',
            'icon' => 'ti-clock',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'updated_at' => [
            'title' => 'Thời gian xử lý',
            'icon' => 'ti-clock',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'contact' => [
        'bank_name' => [
            'title' => 'Tên ngân hàng',
            'icon' => 'ti-building',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'bank_account' => [
            'title' => 'Tên chủ thẻ',
            'icon' => 'ti-user',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'bank_account_number' => [
            'title' => 'Số tài khoản',
            'icon' => 'ti-message',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'user' => [
            'title' => 'Khách hàng',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'membership_level' => [
        'name' => [
            'title' => 'Tên hạng thành viên',
            'icon' => 'ti-diamond',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'min_points' => [
            'title' => 'Số điểm xét hạng để được thăng hạng',
            'icon' => 'ti-coins',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'shipping_rate' => [
        'province' => [
            'title' => 'Tỉnh/Thành phố',
            'icon' => 'ti-building',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'ward' => [
            'title' => 'Thành phố/Khu vực',
            'icon' => 'ti-building',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'price' => [
            'title' => 'Giá vận chuyển',
            'icon' => 'ti-truck',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',  // biểu tượng cài đặt
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'voucher_program' => [
        'name' => [
            'title' => 'Tên',
            'icon' => 'ti-ticket',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'expiration_days' => [
            'title' => 'Số ngày hết hạn',
            'icon' => 'ti-calendar-event',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'min_order_amount' => [
            'title' => 'Giá trị đơn hàng tối thiểu',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'max_discount_value' => [
            'title' => 'Giá trị giảm giá tối đa',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'voucher_type' => [
            'title' => 'Loại voucher',
            'icon' => 'ti-category',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'type' => [
            'title' => 'Loại giảm giá',
            'icon' => 'ti-tag',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'discount_value' => [
            'title' => 'Giá trị giảm',
            'icon' => 'ti-cash-banknote',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',  // biểu tượng cài đặt
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'voucher' => [
        'code' => [
            'title' => 'Mã',
            'icon' => 'ti-ticket',  // biểu tượng mã giảm giá
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'user' => [
            'title' => 'Khách hàng',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày phát hành',
            'icon' => 'ti-calendar-event',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => true
        ],
        'date_end' => [
            'title' => 'Ngày kết thúc',
            'icon' => 'ti-calendar-off',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => true
        ],
        'is_used' => [
            'title' => 'Trạng thái sử dụng',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'min_order_amount' => [
            'title' => 'Giá trị đơn hàng tối thiểu',
            'icon' => 'ti-currency-dollar',  // biểu tượng giá trị đơn hàng
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'voucher_type' => [
            'title' => 'Loại voucher',
            'icon' => 'ti-category',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'type' => [
            'title' => 'Loại giảm giá',
            'icon' => 'ti-tag',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'discount_value' => [
            'title' => 'Giá trị giảm',
            'icon' => 'ti-cash-banknote',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',  // biểu tượng cài đặt
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'discount' => [
        'code' => [
            'title' => 'Mã',
            'icon' => 'ti-discount-2',  // biểu tượng mã giảm giá
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'date_start' => [
            'title' => 'Ngày bắt đầu',
            'icon' => 'ti-calendar-event',  // biểu tượng ngày bắt đầu
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => true
        ],
        'date_end' => [
            'title' => 'Ngày kết thúc',
            'icon' => 'ti-calendar-off',  // biểu tượng ngày kết thúc
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => true
        ],
        'max_usage' => [
            'title' => 'Số lượng phiếu',
            'icon' => 'ti-ticket',  // biểu tượng số lượng phiếu
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'min_order_amount' => [
            'title' => 'Giá trị đơn hàng tối thiểu',
            'icon' => 'ti-currency-dollar',  // biểu tượng giá trị đơn hàng
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'type' => [
            'title' => 'Loại',
            'icon' => 'ti-tag',  // biểu tượng loại giảm giá
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'discount_value' => [
            'title' => 'Giá trị giảm',
            'icon' => 'ti-cash-banknote',  // biểu tượng giá trị giảm giá
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',  // biểu tượng cài đặt
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'flash_sale' => [
        'name' => [
            'title' => 'Tên Flash Sale',
            'icon' => 'ti-bolt',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'start_time' => [
            'title' => 'Thời gian bắt đầu',
            'icon' => 'ti-clock-bolt',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'end_time' => [
            'title' => 'Thời gian kết thúc',
            'icon' => 'ti-clock-x',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'review' => [
        'user' => [
            'title' => 'Khách hàng',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'product' => [
            'title' => 'Sản phẩm',
            'icon' => 'ti-brand-producthunt',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'rating' => [
            'title' => 'Số sao đánh giá',
            'icon' => 'ti-star',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'content' => [
            'title' => 'Bình luận',
            'icon' => 'ti-message',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'icon' => 'ti-calendar',
            'title' => 'Ngày đánh giá',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'installment_types' => [
        'name' => [
            'title' => 'Tên loại trả góp',
            'icon' => 'ti-tag',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'duration_months' => [
            'title' => 'Tổng số tháng trả góp',
            'icon' => 'ti-clock',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'monthly_percentage' => [
            'title' => 'Phần trăm đơn hàng phải trả mỗi tháng',
            'icon' => 'ti-percentage',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'description' => [
            'title' => 'Mô tả',
            'icon' => 'ti-message',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'notification' => [
        'checkbox' => [
            'title' => '',
            'icon' => '',
            'orderable' => false,
            'exportable' => false,
            'width' => '50px',
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
        ],
        'id' => [
            'title' => 'Mã',
            'icon' => 'ti-discount-2',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'title' => [
            'title' => 'Tiêu đề',
            'icon' => 'ti-bell',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'short_message' => [
            'title' => 'Mô tả ngắn',
            'icon' => 'ti-message',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'user' => [
            'title' => 'Khách hàng nhận',
            'icon' => 'ti-user',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-flag',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'created_at' => [
            'title' => 'Ngày thông báo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'bank' => [
        'logo' => [
            'title' => 'Logo',
            'icon' => 'ti-photo',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'name' => [
            'title' => 'Tên ngân hàng',
            'icon' => 'ti-bell',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'code' => [
            'title' => 'Mã ngân hàng',
            'icon' => 'ti-shield',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'bank_account' => [
            'title' => 'Tên chủ thẻ',
            'icon' => 'ti-user',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'bank_account_number' => [
            'title' => 'Số tài khoản',
            'icon' => 'ti-message',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'is_active' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-flag',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'module' => [
        'id' => [
            'title' => 'ID',
            'icon' => 'ti-hash',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'name' => [
            'title' => 'Tên Module',
            'icon' => 'ti-box',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-check',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'permission' => [
        'id' => [
            'title' => 'ID',
            'icon' => 'ti-hash',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'title' => [
            'title' => 'Tên quyền',
            'icon' => 'ti-lock',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'name' => [
            'title' => 'Slug ( Permission_name )',
            'icon' => 'ti-tag',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'module_id' => [
            'title' => 'Thuộc Module',
            'icon' => 'ti-folder',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'guard_name' => [
            'title' => 'Nhóm quyền ( Guard Name )',
            'icon' => 'ti-shield',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'admin' => [
        'fullname' => [
            'title' => 'Họ tên',
            'icon' => 'ti-user',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'phone' => [
            'title' => 'Số điện thoại',
            'icon' => 'ti-phone',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'email' => [
            'title' => 'Email',
            'icon' => 'ti-mail',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'roles' => [
            'title' => 'Vai trò',
            'icon' => 'ti-users',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'visible' => false,
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'user' => [
        'fullname' => [
            'title' => 'Họ tên',
            'icon' => 'ti-user',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'email' => [
            'title' => 'Email',
            'icon' => 'ti-mail',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'phone' => [
            'title' => 'Số điện thoại',
            'icon' => 'ti-phone',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'is_email_verified' => [
            'title' => 'Email đã xác thực',
            'icon' => 'ti-mail-check',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'is_phone_verified' => [
            'title' => 'Số điện thoại đã xác thực',
            'icon' => 'ti-phone-check',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'gender' => [
            'title' => 'Giới tính',
            'icon' => 'ti-gender',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
            'visible' => false
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'category' => [
        'name' => [
            'title' => 'Tên danh mục',
            'icon' => 'ti-folder',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'avatar' => [
            'title' => 'Hình ảnh',
            'icon' => 'ti-photo',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'is_active' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'align-middle text-center',
            'visible' => false
        ],
        'icon' => [
            'title' => 'Icon',
            'icon' => 'ti-star',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'products' => [
            'title' => 'Danh sách sản phẩm',
            'icon' => 'ti-package',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'attribute' => [
        'position' => [
            'title' => 'Vị trí',
            'icon' => 'ti-layers-intersect',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'name' => [
            'title' => 'Tên thuộc tính',
            'icon' => 'ti-tag',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'type' => [
            'title' => 'Loại',
            'icon' => 'ti-clipboard-list',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'variations' => [
            'title' => 'Các biến thể',
            'icon' => 'ti-list-check',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'attributes_variations' => [
        'position' => [
            'title' => 'Vị trí',
            'icon' => 'ti-layers-intersect',
            'orderable' => false,
        ],
        'name' => [
            'title' => 'Tên biến thể',
            'icon' => 'ti-tag',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'desc' => [
            'title' => 'Mô tả',
            'icon' => 'ti-description',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'product' => [
        'avatar' => [
            'title' => 'Ảnh',
            'icon' => 'ti-photo',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'name' => [
            'title' => 'Tên',
            'icon' => 'ti-tag',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'type' => [
            'title' => 'Loại',
            'icon' => 'ti-box',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'is_active' => [
            'title' => 'Trạng thái hoạt động',
            'icon' => 'ti-box',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'price' => [
            'title' => 'Giá',
            'width' => '150px',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'categories' => [
            'title' => 'Danh mục',
            'icon' => 'ti-folder',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'order' => [
        'id' => [
            'title' => 'Mã đơn hàng',
            'icon' => 'ti-receipt',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'user' => [
            'title' => 'Khách hàng',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'payment_method' => [
            'title' => 'PT Thanh toán',
            'icon' => 'ti-credit-card',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'total' => [
            'title' => 'Tổng',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Thời gian đặt',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'commission_withdrawal' => [
        'id' => [
            'title' => 'Mã giao dịch',
            'icon' => 'ti-receipt',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'user' => [
            'title' => 'Khách hàng',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'amount' => [
            'title' => 'Số tiền rút',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-box',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'bank_account_number' => [
            'title' => 'Số tài khoản thụ hưởng',
            'icon' => 'ti-cash-banknote',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'processed_at' => [
            'title' => 'Thời gian xử lý',
            'icon' => 'ti-clock',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'affiliate_order' => [
        'order' => [
            'title' => 'Mã đơn hàng',
            'icon' => 'ti-receipt',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'product' => [
            'title' => 'Sản phẩm',
            'icon' => 'ti-box',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'total' => [
            'title' => 'Tổng tiền hàng',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'affiliate_earning' => [
            'title' => 'Tiền hoa hồng nhận được',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'order_user' => [
        'id' => [
            'title' => 'Mã đơn',
            'icon' => 'ti-receipt',
            'width' => '100px',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'payment_method' => [
            'title' => 'PT thanh toán',
            'icon' => 'ti-credit-card',
            'orderable' => false,
            'width' => '200px',
            'addClass' => 'text-center align-middle',
        ],
        'payment_status' => [
            'title' => 'TT thanh toán',
            'width' => '150px',
            'icon' => 'ti-cards',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'total' => [
            'title' => 'Tổng tiền hàng',
            'icon' => 'ti-currency-dollar',
            'width' => '100px',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Thời gian đặt',
            'icon' => 'ti-clock',
            'orderable' => false,
            'visible' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => '',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'slider' => [
        'name' => [
            'title' => 'Tên',
            'icon' => 'ti-photo',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'plain_key' => [
            'title' => 'Key',
            'icon' => 'ti-key',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'items' => [
            'title' => 'Slider Item',
            'icon' => 'ti-slideshow',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'visible' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'slider_item' => [
        'title' => [
            'title' => 'Tên',
            'icon' => 'ti-tag',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'avatar' => [
            'title' => 'Hình ảnh',
            'icon' => 'ti-photo',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'position' => [
            'title' => 'Vị trí',
            'icon' => 'ti-layers-intersect',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'visible' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'post' => [
        'avatar' => [
            'title' => 'Ảnh',
            'icon' => 'ti-photo',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'title' => [
            'title' => 'Tiêu đề',
            'icon' => 'ti-file',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'is_featured' => [
            'title' => 'Nổi bật',
            'icon' => 'ti-star',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
    ],
    'post_category' => [
        'avatar' => [
            'title' => 'Ảnh đại diện',
            'icon' => 'ti-photo',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'name' => [
            'title' => 'Tên danh mục',
            'icon' => 'ti-folder',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'section' => [
        'avatar' => [
            'title' => 'Ảnh',
            'icon' => 'ti-photo',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'title' => [
            'title' => 'Tiêu đề',
            'icon' => 'ti-file',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'is_rightside' => [
            'title' => 'Ảnh nằm bên',
            'icon' => 'ti-star',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
        'is_active' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'position' => [
            'title' => 'Vị trí',
            'icon' => 'ti-cell',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],
    'role' => [
        'title' => [
            'title' => 'Tên vai trò',
            'icon' => 'ti-user',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'align-middle text-center'
        ],
        'name' => [
            'title' => 'Slug (role_name)',
            'icon' => 'ti-tag',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'align-middle text-center'
        ],
        'guard_name' => [
            'title' => 'Vai trò của nhóm (Guard Name)',
            'icon' => 'ti-shield',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'align-middle text-center'
        ],
    ],
    'transaction' => [
        'checkbox' => [
            'title' => '',
            'icon' => 'ti-square',
            'orderable' => false,
            'exportable' => false,
            'width' => '50px',
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
        ],
        'code' => [
            'title' => 'Mã lệnh',
            'icon' => 'ti-key',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'user' => [
            'title' => 'Người tạo',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'amount' => [
            'title' => 'Số tiền',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'type' => [
            'title' => 'Loại',
            'icon' => 'ti-exchange',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'visible' => false,
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'paid_at' => [
            'title' => 'Ngày thanh toán',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'due_date' => [
            'title' => 'Hạn thanh toán',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'align-middle text-center'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'wallet_transaction' => [
        'user' => [
            'title' => 'Khách hàng',
            'icon' => 'ti-user',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'amount' => [
            'title' => 'Số tiền',
            'icon' => 'ti-currency-dollar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'type' => [
            'title' => 'Loại',
            'icon' => 'ti-exchange',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'icon' => 'ti-toggle-right',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'icon' => 'ti-calendar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'icon' => 'ti-settings',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
];
