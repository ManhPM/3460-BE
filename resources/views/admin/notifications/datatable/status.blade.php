    <span @class([
        'badge',
        App\Enums\Notification\NotificationStatus::from($status)->badge(),
    ])>
        {{ \App\Enums\Notification\NotificationStatus::getDescription($status) }}</span>
