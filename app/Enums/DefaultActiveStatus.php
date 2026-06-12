<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum DefaultActiveStatus: int
{
    use Enum;

    case Active = 1;
    case UnActive = 2;


    public function badge(): string
    {
        return match ($this) {
            DefaultActiveStatus::Active => 'bg-green',
            DefaultActiveStatus::UnActive => 'bg-red',
        };
    }
}
