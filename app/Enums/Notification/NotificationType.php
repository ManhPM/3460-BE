<?php

namespace App\Enums\Notification;

use App\Supports\Enum;

enum NotificationType: int
{
    use Enum;
    case All = 1;
    case Customer = 2;
}
