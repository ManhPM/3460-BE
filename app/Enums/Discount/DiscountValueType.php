<?php

namespace App\Enums\Discount;


use App\Admin\Support\Enum;

enum DiscountValueType: string
{
    use Enum;

    case Percent = 'percent';
    case Money = 'money';

    public function badge(): string
    {
        return match ($this) {
            DiscountValueType::Percent => 'bg-orange',
            DiscountValueType::Money => 'bg-green',
        };
    }
}
