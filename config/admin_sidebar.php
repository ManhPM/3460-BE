<?php

$data = [
    [
        'title' => 'Dashboard',
        'routeName' => 'admin.dashboard',
        'icon' => '<i class="ti ti-home"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => []
    ],
    [
        'title' => 'Hạng thành viên',
        'routeName' => null,
        'icon' => '<i class="ti ti-diamond"></i>',
        'roles' => [],
        'permissions' => [
            'createMembershipLevel',
            'viewMembershipLevel',
            'updateMembershipLevel',
            'deleteMembershipLevel',
        ],
        'sub' => [
            [
                'title' => 'Thêm Hạng thành viên',
                'routeName' => 'admin.membership_level.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createMembershipLevel'],
            ],
            [
                'title' => 'DS Hạng thành viên',
                'routeName' => 'admin.membership_level.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewMembershipLevel'],
            ],
            [
                'title' => 'Cài đặt',
                'routeName' => 'admin.setting.membershipLevel',
                'icon' => '<i class="ti ti-tool"></i>',
                'roles' => [],
                'permissions' => ['settingMembershipLevel'],
            ],
        ]
    ],
    [
        'title' => 'Giá giao hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-truck"></i>',
        'roles' => [],
        'permissions' => [
            'createShippingRates',
            'viewShippingRates',
            'updateShippingRates',
            'deleteShippingRates',
        ],
        'sub' => [
            [
                'title' => 'Thêm Giá giao hàng',
                'routeName' => 'admin.shipping_rate.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createShippingRates'],
            ],
            [
                'title' => 'DS Giá giao hàng',
                'routeName' => 'admin.shipping_rate.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewShippingRates'],
            ],
        ]
    ],
    // [
    //     'title' => 'Section trang chủ',
    //     'routeName' => null,
    //     'icon' => '<i class="ti ti-section"></i>',
    //     'roles' => [],
    //     'permissions' => [
    //         'createSection',
    //         'viewSection',
    //         'updateSection',
    //         'deleteSection',
    //     ],
    //     'sub' => [
    //         [
    //             'title' => 'Thêm Section trang chủ',
    //             'routeName' => 'admin.section.create',
    //             'icon' => '<i class="ti ti-plus"></i>',
    //             'roles' => [],
    //             'permissions' => ['createSection'],
    //         ],
    //         [
    //             'title' => 'DS Section trang chủ',
    //             'routeName' => 'admin.section.index',
    //             'icon' => '<i class="ti ti-list"></i>',
    //             'roles' => [],
    //             'permissions' => ['viewSection'],
    //         ],
    //     ]
    // ],
    [
        'title' => 'Ngân hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-cash-banknote"></i>',
        'roles' => [],
        'permissions' => ['createBank', 'viewBank', 'updateBank', 'deleteBank'],
        'sub' => [
            [
                'title' => 'DS Ngân hàng',
                'routeName' => 'admin.bank.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['createBank'],
            ],
        ]
    ],
    [
        'title' => 'Thông báo',
        'routeName' => null,
        'icon' => '<i class="ti ti-bell-ringing"></i>',
        'roles' => [],
        'permissions' => [
            'createNotification',
            'viewNotification',
            'updateNotification',
            'deleteNotification',
        ],
        'sub' => [
            [
                'title' => 'Thêm Thông báo',
                'routeName' => 'admin.notification.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createNotification'],
            ],
            [
                'title' => 'DS Thông báo',
                'routeName' => 'admin.notification.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],
        ]
    ],
    [
        'title' => 'Voucher',
        'routeName' => null,
        'icon' => '<i class="ti ti-ticket"></i>',
        'roles' => [],
        'permissions' => [
            'createVoucher',
            'viewVoucher',
            'updateVoucher',
            'deleteVoucher',
            'createVoucherProgram',
            'viewVoucherProgram',
            'updateVoucherProgram',
            'deleteVoucherProgram'
        ],
        'sub' => [
            [
                'title' => 'Thêm Voucher',
                'routeName' => 'admin.voucher.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createVoucher'],
            ],
            [
                'title' => 'DS Voucher',
                'routeName' => 'admin.voucher.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewVoucher'],
            ],
            [
                'title' => 'Thêm Chương trình',
                'routeName' => 'admin.voucher_program.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createVoucherProgram'],
            ],
            [
                'title' => 'DS Chương trình',
                'routeName' => 'admin.voucher_program.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewVoucherProgram'],
            ],
        ]
    ],
    [
        'title' => 'Mã giảm giá',
        'routeName' => null,
        'icon' => '<i class="ti ti-discount"></i>',
        'roles' => [],
        'permissions' => ['createDiscountCode', 'viewDiscountCode', 'updateDiscountCode', 'deleteDiscountCode'],
        'sub' => [
            [
                'title' => 'Thêm Mã giảm giá',
                'routeName' => 'admin.discount.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createDiscountCode'],
            ],
            [
                'title' => 'DS Mã giảm giá',
                'routeName' => 'admin.discount.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewDiscountCode'],
            ],
        ]
    ],
    [
        'title' => 'Giao dịch',
        'routeName' => null,
        'icon' => '<i class="ti ti-currency-dollar"></i>',
        'roles' => [],
        'permissions' => ['createTransaction', 'viewTransaction', 'updateTransaction', 'deleteTransaction'],
        'sub' => [
            [
                'title' => 'Giao dịch ví',
                'routeName' => 'admin.wallet_transaction.index',
                'icon' => '<i class="ti ti-wallet"></i>',
                'roles' => [],
                'permissions' => ['viewTransaction'],
            ],
        ]
    ],
    [
        'title' => 'Đơn hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-box"></i>',
        'roles' => [],
        'permissions' => ['createOrder', 'viewOrder', 'updateOrder', 'deleteOrder'],
        'sub' => [
            [
                'title' => 'Thêm Đơn hàng',
                'routeName' => 'admin.order.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createOrder'],
            ],
            [
                'title' => 'DS Đơn hàng',
                'routeName' => 'admin.order.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewOrder'],
            ],
        ]
    ],
    [
        'title' => 'Sản phẩm',
        'routeName' => null,
        'icon' => '<i class="ti ti-brand-producthunt"></i>',
        'roles' => [],
        'permissions' => [
            'createProduct',
            'viewProduct',
            'updateProduct',
            'deleteProduct',
            'createProductCategory',
            'updateProductCategory',
            'viewProductCategory'
        ],
        'sub' => [
            [
                'title' => 'Thêm Sản phẩm',
                'routeName' => 'admin.product.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createProduct'],
            ],
            [
                'title' => 'DS Sản phẩm',
                'routeName' => 'admin.product.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewProduct'],
            ],
            [
                'title' => 'Quản lý tồn kho',
                'routeName' => 'admin.inventory.index',
                'icon' => '<i class="ti ti-building-warehouse"></i>',
                'roles' => [],
                'permissions' => ['viewProduct'],
            ],
            [
                'title' => 'DS Thuộc tính',
                'routeName' => 'admin.attribute.index',
                'icon' => '<i class="ti ti-clipboard-list"></i>',
                'roles' => [],
                'permissions' => ['viewProductAttribute'],
            ],
            [
                'title' => 'DS Danh mục',
                'routeName' => 'admin.category.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewProductCategory'],
            ],
        ]
    ],
    [
        'title' => 'Khách hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-users"></i>',
        'roles' => [],
        'permissions' => ['createUser', 'viewUser', 'updateUser', 'deleteUser'],
        'sub' => [
            [
                'title' => 'Thêm Khách hàng',
                'routeName' => 'admin.user.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createUser'],
            ],
            [
                'title' => 'DS Khách hàng',
                'routeName' => 'admin.user.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewUser'],
            ],
        ]
    ],
    [
        'title' => 'FlashSale',
        'routeName' => null,
        'icon' => '<i class="ti ti-bolt"></i>',
        'roles' => [],
        'permissions' => ['createFlashSale', 'viewFlashSale', 'updateFlashSale', 'deleteFlashSale'],
        'sub' => [
            [
                'title' => 'Thêm FlashSale',
                'routeName' => 'admin.flashsale.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createFlashSale'],
            ],
            [
                'title' => 'DS FlashSale',
                'routeName' => 'admin.flashsale.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewFlashSale'],
            ],
        ]
    ],
    [
        'title' => 'Đánh giá',
        'routeName' => null,
        'icon' => '<i class="ti ti-star"></i>',
        'roles' => [],
        'permissions' => ['createReview', 'viewReview', 'updateReview', 'deleteReview'],
        'sub' => [
            [
                'title' => 'DS Đánh giá',
                'routeName' => 'admin.review.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['createReview'],
            ],
        ]
    ],
    [
        'title' => 'Bài viết',
        'routeName' => null,
        'icon' => '<i class="ti ti-article"></i>',
        'roles' => [],
        'permissions' =>
        [
            'createPost',
            'viewPost',
            'updatePost',
            'deletePost',
            'viewPostCategory',
            'createPostCategory',
            'updatePostCategory'
        ],
        'sub' => [
            [
                'title' => 'Thêm Bài viết',
                'routeName' => 'admin.post.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPost'],
            ],
            [
                'title' => 'DS Bài viết',
                'routeName' => 'admin.post.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPost'],
            ],
            [
                'title' => 'DS Chuyên mục',
                'routeName' => 'admin.post_category.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPostCategory'],
            ]
        ]
    ],
    [
        'title' => 'Sliders',
        'routeName' => null,
        'icon' => '<i class="ti ti-slideshow"></i>',
        'roles' => [],
        'permissions' => ['createSlider', 'viewSlider', 'updateSlider', 'deleteSlider'],
        'sub' => [
            [
                'title' => 'Thêm Sliders',
                'routeName' => 'admin.slider.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createSlider'],
            ],
            [
                'title' => 'DS Sliders',
                'routeName' => 'admin.slider.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewSlider'],
            ],
        ]
    ],
    // [
    //     'title' => 'Vai trò',
    //     'routeName' => null,
    //     'icon' => '<i class="ti ti-user-check"></i>',
    //     'roles' => [],
    //     'permissions' => ['createRole', 'viewRole', 'updateRole', 'deleteRole'],
    //     'sub' => [
    //         [
    //             'title' => 'Thêm Vai trò',
    //             'routeName' => 'admin.role.create',
    //             'icon' => '<i class="ti ti-plus"></i>',
    //             'roles' => [],
    //             'permissions' => ['createRole'],
    //         ],
    //         [
    //             'title' => 'DS Vai trò',
    //             'routeName' => 'admin.role.index',
    //             'icon' => '<i class="ti ti-list"></i>',
    //             'roles' => [],
    //             'permissions' => ['viewRole'],
    //         ]
    //     ]
    // ],
    [
        'title' => 'Admin',
        'routeName' => null,
        'icon' => '<i class="ti ti-user-shield"></i>',
        'roles' => [],
        'permissions' => ['createAdmin', 'viewAdmin', 'updateAdmin', 'deleteAdmin'],
        'sub' => [
            [
                'title' => 'Thêm Admin',
                'routeName' => 'admin.admin.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createAdmin'],
            ],
            [
                'title' => 'DS Admin',
                'routeName' => 'admin.admin.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewAdmin'],
            ],
        ]
    ],
    // [
    //     'title' => 'Quyền',
    //     'routeName' => null,
    //     'icon' => '<i class="ti ti-code"></i>',
    //     'roles' => [],
    //     'permissions' => ['mevivuDev'],
    //     'sub' => [
    //         [
    //             'title' => 'Thêm Quyền',
    //             'routeName' => 'admin.permission.create',
    //             'icon' => '<i class="ti ti-plus"></i>',
    //             'roles' => [],
    //             'permissions' => ['mevivuDev'],
    //         ],
    //         [
    //             'title' => 'DS Quyền',
    //             'routeName' => 'admin.permission.index',
    //             'icon' => '<i class="ti ti-list"></i>',
    //             'roles' => [],
    //             'permissions' => ['mevivuDev'],
    //         ]
    //     ]
    // ],
    // [
    //     'title' => 'Module',
    //     'routeName' => null,
    //     'icon' => '<i class="ti ti-code"></i>',
    //     'roles' => [],
    //     'permissions' => ['mevivuDev'],
    //     'sub' => [
    //         [
    //             'title' => 'Thêm Module',
    //             'routeName' => 'admin.module.create',
    //             'icon' => '<i class="ti ti-plus"></i>',
    //             'roles' => [],
    //             'permissions' => ['mevivuDev'],
    //         ],
    //         [
    //             'title' => 'DS Module',
    //             'routeName' => 'admin.module.index',
    //             'icon' => '<i class="ti ti-list"></i>',
    //             'roles' => [],
    //             'permissions' => ['mevivuDev'],
    //         ]
    //     ]
    // ],
    [
        'title' => 'Cài đặt',
        'routeName' => null,
        'icon' => '<i class="ti ti-settings"></i>',
        'roles' => [],
        'permissions' => ['settingGeneral'],
        'sub' => [
            // [
            //     'title' => 'Cấu hình Zalo MiniApp',
            //     'routeName' => 'admin.setting.zaloMiniAppConfig',
            //     'icon' => '<i class="ti ti-tool"></i>',
            //     'roles' => [],
            //     'permissions' => ['settingMiniapp'],
            // ],
            // [
            //     'title' => 'Cài đặt web admin',
            //     'routeName' => 'admin.setting.theme',
            //     'icon' => '<i class="ti ti-tool"></i>',
            //     'roles' => [],
            //     'permissions' => ['settingTheme'],
            // ],
            // [
            //     'title' => 'Cài đặt web bán hàng',
            //     'routeName' => 'admin.setting.webTheme',
            //     'icon' => '<i class="ti ti-tool"></i>',
            //     'roles' => [],
            //     'permissions' => ['settingTheme'],
            // ],
            [
                'title' => 'Cấu hình',
                'routeName' => 'admin.setting.config',
                'icon' => '<i class="ti ti-tool"></i>',
                'roles' => [],
                'permissions' => ['settingConfig'],
            ],
            [
                'title' => 'Chung',
                'routeName' => 'admin.setting.general',
                'icon' => '<i class="ti ti-tool"></i>',
                'roles' => [],
                'permissions' => ['settingGeneral'],
            ],
            // [
            //     'title' => 'Chân trang',
            //     'routeName' => 'admin.setting.footer',
            //     'icon' => '<i class="ti ti-tool"></i>',
            //     'roles' => [],
            //     'permissions' => ['settingFooter'],
            // ],
            // [
            //     'title' => 'Thông tin liên hệ',
            //     'routeName' => 'admin.setting.contact',
            //     'icon' => '<i class="ti ti-tool"></i>',
            //     'roles' => [],
            //     'permissions' => ['settingContact'],
            // ],
            // [
            //     'title' => 'Trang giới thiệu',
            //     'routeName' => 'admin.setting.information',
            //     'icon' => '<i class="ti ti-tool"></i>',
            //     'roles' => [],
            //     'permissions' => ['settingInformation'],
            // ],
        ]
    ],
];

$isCombo = env('IS_COMBO');
$isMiniapp = env('IS_MINIAPP');
$isPro = env('IS_PRO');
$isVariation = env('IS_VARIATION');

if (!$isVariation) {
    $unsetArr = [
        'DS Thuộc tính',
    ];

    foreach ($data as $key => &$item) {
        $title = $item['title'] ?? null;

        // Xóa mục cha nếu có trong danh sách unset
        if (in_array($title, $unsetArr)) {
            unset($data[$key]);
            continue;
        }

        // Kiểm tra và xóa các mục con nếu có trong danh sách unset
        if (!empty($item['sub'])) {
            foreach ($item['sub'] as $subKey => $subItem) {
                if (in_array($subItem['title'], $unsetArr)) {
                    unset($item['sub'][$subKey]);
                }
            }
            // Nếu sub rỗng sau khi lọc, xóa luôn
            if (empty($item['sub'])) {
                unset($item['sub']);
            }
        }
    }
    unset($item); // Xóa tham chiếu để tránh lỗi khi làm việc với foreach có tham chiếu
}

if (!$isPro) {
    $unsetArr = [
        'Đăng ký hồ sơ Affiliate',
        'Cập nhật hồ sơ Affiliate',
        'Huỷ hồ sơ Affiliate',
        'Voucher',
        'Mã giảm giá',
        'Giao dịch',
        'Cấu hình Zalo MiniApp',
        'DS Đơn hàng hoa hồng',
    ];

    foreach ($data as $key => &$item) {
        $title = $item['title'] ?? null;

        // Xóa mục cha nếu có trong danh sách unset
        if (in_array($title, $unsetArr)) {
            unset($data[$key]);
            continue;
        }

        // Kiểm tra và xóa các mục con nếu có trong danh sách unset
        if (!empty($item['sub'])) {
            foreach ($item['sub'] as $subKey => $subItem) {
                if (in_array($subItem['title'], $unsetArr)) {
                    unset($item['sub'][$subKey]);
                }
            }
            // Nếu sub rỗng sau khi lọc, xóa luôn
            if (empty($item['sub'])) {
                unset($item['sub']);
            }
        }
    }
    unset($item); // Xóa tham chiếu để tránh lỗi khi làm việc với foreach có tham chiếu
}

if (!$isCombo) {
    $unsetArr = [
        'Section trang chủ',
        'Cài đặt web bán hàng',
        'Chân trang',
        'Thông tin liên hệ',
        'Trang giới thiệu',
    ];

    foreach ($data as $key => &$item) {
        $title = $item['title'] ?? null;

        if (in_array($title, $unsetArr)) {
            unset($data[$key]);
            continue;
        }

        if (!empty($item['sub'])) {
            foreach ($item['sub'] as $subKey => $subItem) {
                if (in_array($subItem['title'], $unsetArr)) {
                    unset($item['sub'][$subKey]);
                }
            }
            if (empty($item['sub'])) {
                unset($item['sub']);
            }
        }
    }
    unset($item);
}

if (!$isMiniapp) {
    $unsetArr = [
        'Cấu hình Zalo MiniApp',
    ];

    foreach ($data as $key => &$item) {
        $title = $item['title'] ?? null;

        // Xóa mục cha nếu có trong danh sách unset
        if (in_array($title, $unsetArr)) {
            unset($data[$key]);
            continue;
        }

        // Kiểm tra và xóa các mục con nếu có trong danh sách unset
        if (!empty($item['sub'])) {
            foreach ($item['sub'] as $subKey => $subItem) {
                if (in_array($subItem['title'], $unsetArr)) {
                    unset($item['sub'][$subKey]);
                }
            }
            // Nếu sub rỗng sau khi lọc, xóa luôn
            if (empty($item['sub'])) {
                unset($item['sub']);
            }
        }
    }
    unset($item); // Xóa tham chiếu để tránh lỗi khi làm việc với foreach có tham chiếu
}



return $data;
