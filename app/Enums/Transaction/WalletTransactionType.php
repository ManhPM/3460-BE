<?php

namespace App\Enums\Transaction;


use App\Admin\Support\Enum;

enum WalletTransactionType: string
{
    use Enum;

    case Deposit = 'deposit';

    case Withdraw = 'withdraw';

    case Payment = 'payment';

    case Refund = 'refund';

    case Affiliate = 'affiliate';


    public function badge(): string
    {
        return match ($this) {
            WalletTransactionType::Deposit => 'bg-green',
            WalletTransactionType::Withdraw => 'bg-orange',
            WalletTransactionType::Payment => 'bg-yellow',
            WalletTransactionType::Refund => 'bg-purple',
            WalletTransactionType::Affiliate => 'bg-blue',
        };
    }
}
