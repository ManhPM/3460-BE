<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum PriorityStatus: int
{
    use Enum;

    case Priority = 1;
    case NotPriority = 2;


    public function badge(): string
    {
        return match ($this) {
            PriorityStatus::Priority => 'bg-green',
            PriorityStatus::NotPriority => 'bg-red',
        };
    }
}
