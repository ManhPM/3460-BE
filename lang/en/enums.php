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
        WalletTransactionStatus::Pending->value => 'Pending approval',
        WalletTransactionStatus::Approved->value => 'Approved',
        WalletTransactionStatus::Rejected->value => 'Cancelled',
    ],
    WalletTransactionType::class => [
        WalletTransactionType::Deposit->value => 'Deposit',
        WalletTransactionType::Withdraw->value => 'Withdraw',
        WalletTransactionType::Payment->value => 'Payment',
        WalletTransactionType::Refund->value => 'Refund',
        WalletTransactionType::Affiliate->value => 'Affiliate',
    ],
    WalletTransactionStatus::class => [
        WalletTransactionStatus::Pending->value => 'Processing',
        WalletTransactionStatus::Approved->value => 'Approved',
        WalletTransactionStatus::Rejected->value => 'Cancelled',
    ],
    Gender::class => [
        Gender::Male->value => 'Male',
        Gender::Female->value => 'Female',
    ],
    WithdrawStatus::class => [
        WithdrawStatus::Cancelled->value => 'Cancelled',
        WithdrawStatus::Confirmed->value => 'Confirmed',
        WithdrawStatus::Pending->value => 'Pending',
    ],
    NotificationType::class => [
        NotificationType::All->value => 'All',
        NotificationType::Customer->value => 'Specific customers',
    ],
    NotificationStatus::class => [
        NotificationStatus::READ->value => 'Read',
        NotificationStatus::NOT_READ->value => 'Unread',
    ],
    AttributeType::class => [
        AttributeType::Color->value => 'Color',
        AttributeType::Button->value => 'Non-color',
    ],
    DefaultActiveStatus::class => [
        DefaultActiveStatus::Active->value => 'Yes',
        DefaultActiveStatus::UnActive->value => 'No',
    ],
    PaymentStatus::class => [
        PaymentStatus::Unpaid->value => 'Unpaid',
        PaymentStatus::Paid->value => 'Paid',
        PaymentStatus::Pending->value => 'Pending payment approval',
    ],
    TransactionStatus::class => [
        TransactionStatus::Pending->value => 'Processing',
        TransactionStatus::Success->value => 'Success',
        TransactionStatus::Failed->value => 'Failed',
    ],
    SliderStatus::class => [
        SliderStatus::Active => 'Active',
        SliderStatus::UnActive => 'Inactive',
    ],
    PostCategoryStatus::class => [
        PostCategoryStatus::Published => 'Published',
        PostCategoryStatus::Draft => 'Draft',
    ],
    PostStatus::class => [
        PostStatus::Draft->value => 'Draft',
        PostStatus::Published->value => 'Published',
    ],
    PaymentMethod::class => [
        PaymentMethod::Direct->value => 'COD (Cash on Delivery)',
        PaymentMethod::Banking->value => 'Bank Transfer',
        // PaymentMethod::VNPAY->value => 'VNPAY',
        PaymentMethod::Wallet->value => 'Customer Wallet',
    ],
    ProductType::class => [
        ProductType::Simple->value => 'Simple Product',
        ProductType::Variable->value => 'Variable Product'
    ],
    DefaultStatus::class => array(
        DefaultStatus::Published->value => 'Published',
        DefaultStatus::Draft->value => 'Draft',
        DefaultStatus::Deleted->value => 'Deleted',
    ),
    ProductVariationAction::class => [
        ProductVariationAction::AddSimple => 'Add Variation',
        ProductVariationAction::AddFromAllVariations => 'Create variations from all attributes'
    ],
    OrderStatus::class => [
        OrderStatus::Pending->value  => 'Pending confirmation',
        OrderStatus::Confirmed->value => 'Confirmed',
        OrderStatus::Completed->value => 'Completed',
        OrderStatus::Delivering->value => 'Delivering',
        OrderStatus::Cancelled->value => 'Cancelled',
    ],
    DiscountValueType::class => [
        DiscountValueType::Money->value => 'Money',
        DiscountValueType::Percent->value => 'Percent'
    ],
    VoucherType::class => [
        VoucherType::Product->value => 'Product discount',
        VoucherType::Shipping->value => 'Shipping discount'
    ],
    PriorityStatus::class => [
        PriorityStatus::Priority->value => 'Priority',
        PriorityStatus::NotPriority->value => 'Not priority'
    ],
    ModuleStatus::class => [
        ModuleStatus::ChuaXong => 'Not completed',
        ModuleStatus::DaXong => 'Completed',
        ModuleStatus::DaDuyet => 'Approved'
    ],
];
