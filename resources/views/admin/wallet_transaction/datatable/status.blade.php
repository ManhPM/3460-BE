<span @class([
    'badge',
    App\Enums\Transaction\WalletTransactionStatus::from($status)->badge(),
])>{{ \App\Enums\Transaction\WalletTransactionStatus::getDescription($status) }}</span>
