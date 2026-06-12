<?php

namespace App\Admin\Repositories\Notification;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Notification;

class NotificationRepository extends EloquentRepository implements NotificationRepositoryInterface
{

    public function getModel(): string
    {
        return Notification::class;
    }

    public function getByAdminIdAndPaginate($adminId)
    {
        return $this->model->where('admin_id', $adminId)->orderBy('created_at', 'DESC')->paginate(4);
    }

    public function getByUserIdAndPaginate($userId)
    {
        return $this->model->where('user_id', $userId)->orderBy('created_at', 'DESC')->paginate(8);
    }

    public function paginateAdmin($adminId, $perPage = 3)
    {
        return $this->model->where('admin_id', $adminId)->orderBy('created_at', 'DESC')->paginate($perPage);
    }
}
