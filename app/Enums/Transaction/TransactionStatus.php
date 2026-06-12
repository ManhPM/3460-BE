<?php

namespace App\Enums\Transaction;


use App\Admin\Support\Enum;

enum TransactionStatus: int
{
    use Enum;

    case Pending = 1;

    case Success = 2;

    case Failed = 3;
}
