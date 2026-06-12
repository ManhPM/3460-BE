<?php

namespace App\Enums\Product;

use App\Supports\Enum;

enum ProductType: int
{
    use Enum;
    case Simple = 1;
    case Variable = 2;
}
