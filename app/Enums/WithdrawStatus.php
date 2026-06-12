<?php

namespace App\Enums;

use App\Supports\Enum;

enum WithdrawStatus: string
{
    use Enum;

    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';

    public function badge(): string
    {
        return match ($this) {
            self::Confirmed => 'bg-green',
            self::Cancelled => 'bg-red',
            self::Pending => 'bg-orange',
        };
    }
}
