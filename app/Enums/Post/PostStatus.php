<?php

namespace App\Enums\Post;

use App\Admin\Support\Enum;

enum PostStatus: int
{
    use Enum;

    case Published = 1;
    case Draft = 2;

    public function badge(): string
    {
        return match ($this) {
            PostStatus::Published => 'bg-green',
            PostStatus::Draft => 'bg-red',
        };
    }
}
