<?php

namespace App\Enums\Attribute;

use App\Admin\Support\Enum;

enum AttributeType: int
{
    use Enum;

    case Button = 1;
    case Color = 2;

    public function badge(): string
    {
        return match($this) {
            AttributeType::Button => 'bg-green',
            AttributeType::Color => '',
        };
    }
}
