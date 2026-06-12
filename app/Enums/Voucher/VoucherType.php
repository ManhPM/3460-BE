<?php

namespace App\Enums\Voucher;


use App\Admin\Support\Enum;

enum VoucherType: string
{
    use Enum;

    case Product = 'product';
    case Shipping = 'shipping';

    public function badge(): string
    {
        return match ($this) {
            VoucherType::Product => 'bg-orange',
            VoucherType::Shipping => 'bg-green',
        };
    }
}
