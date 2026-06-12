<?php

namespace App\Api\AdminV1\Repositories\Notification;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface NotificationRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

