<span @class(['badge', App\Enums\WithdrawStatus::from($status)->badge()])>
    {{ \App\Enums\WithdrawStatus::getDescription($status) }}</span>
