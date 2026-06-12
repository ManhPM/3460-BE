<?php

namespace App\Enums\Notification;

use App\Supports\Enum;

enum NotificationStatus: string
{
    use Enum;

    case NOT_READ = 'not_read';
    case READ = 'read';
    public function badge()
    {
        return match ($this) {
            NotificationStatus::NOT_READ => '',
            NotificationStatus::READ => 'bg-green',
        };
    }
}
