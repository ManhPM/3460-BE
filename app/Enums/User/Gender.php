<?php

namespace App\Enums\User;


use App\Supports\Enum;

enum Gender: string
{
    use Enum;

    case Male = 'male';
    case Female = 'female';
}
