<?php

use App\Enums\Attribute\AttributeType;
use App\Enums\DefaultActiveStatus;
use App\Enums\DefaultStatus;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Module\ModuleStatus;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Order\PaymentStatus;
use App\Enums\Post\PostStatus;
use App\Enums\PostCategory\PostCategoryStatus;
use App\Enums\PriorityStatus;
use App\Enums\Product\{ProductType, ProductVariationAction};
use App\Enums\Slider\SliderStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use App\Enums\User\Gender;
use App\Enums\Voucher\VoucherType;
use App\Enums\WithdrawStatus;

return [
    WalletTransactionStatus::class => [
        WalletTransactionStatus::Pending->value => 'Chờ duyệt',
        WalletTransactionStatus::Approved->value => 'Đã duyệt',
        WalletTransactionStatus::Rejected->value => 'Đã hủy',
    ],
    WalletTransactionType::class => [
        WalletTransactionType::Deposit->value => 'Nạp tiền',
        WalletTransactionType::Withdraw->value => 'Rút tiền',
        WalletTransactionType::Payment->value => 'Thanh toán',
        WalletTransactionType::Refund->value => 'Hoàn tiền',
        WalletTransactionType::Affiliate->value => 'Tiếp thị liên kết',
    ],
    WalletTransactionStatus::class => [
        WalletTransactionStatus::Pending->value => 'Đang xử lý',
        WalletTransactionStatus::Approved->value => 'Đã duyệt',
        WalletTransactionStatus::Rejected->value => 'Đã hủy',
    ],
    Gender::class => [
        Gender::Male->value => 'Nam',
        Gender::Female->value => 'Nữ',
    ],
    WithdrawStatus::class => [
        WithdrawStatus::Cancelled->value => 'Đã huỷ',
        WithdrawStatus::Confirmed->value => 'Đã xác nhận',
        WithdrawStatus::Pending->value => 'Đang chờ',
    ],
    NotificationType::class => [
        NotificationType::All->value => 'Tất cả',
        NotificationType::Customer->value => 'Một vài người cụ thể',
    ],
    NotificationStatus::class => [
        NotificationStatus::READ->value => 'Đã đọc',
        NotificationStatus::NOT_READ->value => 'Chưa đọc',
    ],
    AttributeType::class => [
        AttributeType::Color->value => 'Màu sắc',
        AttributeType::Button->value => 'Không phải màu sắc',
    ],
    DefaultActiveStatus::class => [
        DefaultActiveStatus::Active->value => 'Có',
        DefaultActiveStatus::UnActive->value => 'Không',
    ],
    PaymentStatus::class => [
        PaymentStatus::Unpaid->value => 'Chưa thanh toán',
        PaymentStatus::Paid->value => 'Đã thanh toán',
        PaymentStatus::Pending->value => 'Chờ duyệt thanh toán',
    ],
    TransactionStatus::class => [
        TransactionStatus::Pending->value => 'Đang xử lý',
        TransactionStatus::Success->value => 'Thành công',
        TransactionStatus::Failed->value => 'Thất bại',
    ],
    SliderStatus::class => [
        SliderStatus::Active => 'Đang hoạt động',
        SliderStatus::UnActive => 'Ngưng hoạt động',
    ],
    PostCategoryStatus::class => [
        PostCategoryStatus::Published => 'Đã xuất bản',
        PostCategoryStatus::Draft => 'Bản nháp',
    ],
    PostStatus::class => [
        PostStatus::Draft->value => 'Bản nháp',
        PostStatus::Published->value => 'Đã xuất bản',
    ],
    PaymentMethod::class => [
        PaymentMethod::Direct->value => 'COD (Tiền mặt)',
        PaymentMethod::Banking->value => 'Chuyển khoản ngân hàng',
        // PaymentMethod::VNPAY->value => 'VNPAY',
        PaymentMethod::Wallet->value => 'Ví khách hàng',
    ],
    ProductType::class => [
        ProductType::Simple->value => 'Sản phẩm đơn giản',
        ProductType::Variable->value => 'Sản phẩm có biến thể'
    ],
    DefaultStatus::class => array(
        DefaultStatus::Published->value => 'Đã xuất bản',
        DefaultStatus::Draft->value => 'Bản nháp',
        DefaultStatus::Deleted->value => 'Đã xoá',
    ),
    ProductVariationAction::class => [
        ProductVariationAction::AddSimple => 'Thêm biến thể',
        ProductVariationAction::AddFromAllVariations => 'Tạo biến thể từ tất cả thuộc tính'
    ],
    OrderStatus::class => [
        OrderStatus::Pending->value  => 'Chờ xác nhận',
        OrderStatus::Confirmed->value => 'Đã xác nhận',
        OrderStatus::Completed->value => 'Hoàn thành',
        OrderStatus::Delivering->value => 'Đang giao hàng',
        OrderStatus::Cancelled->value => 'Hủy bỏ',
    ],
    DiscountValueType::class => [
        DiscountValueType::Money->value => 'Tiền',
        DiscountValueType::Percent->value => 'Phần trăm'
    ],
    VoucherType::class => [
        VoucherType::Product->value => 'Giảm giá tiền hàng',
        VoucherType::Shipping->value => 'Giảm giá vận chuyển'
    ],
    PriorityStatus::class => [
        PriorityStatus::Priority->value => 'Ưu tiên',
        PriorityStatus::NotPriority->value => 'Không ưu tiên'
    ],
    ModuleStatus::class => [
        ModuleStatus::ChuaXong => 'Chưa xong',
        ModuleStatus::DaXong => 'Đã xong',
        ModuleStatus::DaDuyet => 'Đã duyệt'
    ],
];
