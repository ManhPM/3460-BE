<?php

namespace App\Admin\Repositories\Notification;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface NotificationRepositoryInterface extends EloquentRepositoryInterface
{
    public function getByAdminIdAndPaginate($adminId);
    public function getByUserIdAndPaginate($userId);
}
