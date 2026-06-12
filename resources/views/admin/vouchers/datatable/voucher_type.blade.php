<span @class([
    'badge',
    App\Enums\Voucher\VoucherType::from($voucher_type)->badge(),
])>{{ \App\Enums\Voucher\VoucherType::getDescription($voucher_type) }}</span>
