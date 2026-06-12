<?php

namespace App\Enums\Payment;

use App\Supports\Enum;

enum PaymentMethod: string
{
    use Enum;
        //Thanh toán trực tiếp
    case Direct = 'direct';
        //VNPAY
        // case VNPAY = 'vnpay';
        //Chuyển khoản ngân hàng
    case Banking = 'banking';
        //Ví nội bộ
    case Wallet = 'wallet';

    public function label(): string
    {
        return match ($this) {
            PaymentMethod::Direct => 'Thanh toán trực tiếp',
            PaymentMethod::Banking => 'Chuyển khoản ngân hàng',
            PaymentMethod::Wallet => 'Ví nội bộ',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            // PaymentMethod::Online => 'bg-green',
            PaymentMethod::Direct => 'bg-red',
            PaymentMethod::Banking => 'bg-orange',
            // PaymentMethod::VNPAY => 'bg-green',
            PaymentMethod::Wallet => 'bg-blue',
        };
    }
}
