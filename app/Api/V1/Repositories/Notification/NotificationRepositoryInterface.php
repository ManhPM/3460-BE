<?php

namespace App\Api\V1\Repositories\Notification;

use App\Admin\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\Request;

interface NotificationRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAll();

    public function delete($id);

    public function getUserNotifications($userId, Request $request);
}
