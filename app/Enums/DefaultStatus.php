<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum DefaultStatus: int
{
    use Enum;

    case Published = 1;
    case Draft = 2;
    case Deleted = 3;


    public function badge(): string
    {
        return match ($this) {
            DefaultStatus::Published => 'bg-green',
            DefaultStatus::Deleted => 'bg-red',
            DefaultStatus::Draft => '',
        };
    }
}
