<?php

namespace App\Enums\Transaction;


use App\Admin\Support\Enum;

enum WalletTransactionStatus: string
{
    use Enum;

    case Pending = 'pending';

    case Approved = 'approved';

    case Rejected = 'rejected';


    public function badge(): string
    {
        return match ($this) {
            WalletTransactionStatus::Pending => 'bg-orange',
            WalletTransactionStatus::Approved => 'bg-green',
            WalletTransactionStatus::Rejected => 'bg-red',
        };
    }
}
