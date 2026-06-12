<?php

namespace App\Api\V1\Repositories\Notification;

use App\Admin\Repositories\Notification\NotificationRepository as AdminNotificationRepository;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationRepository extends AdminNotificationRepository implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $note)
    {
        $this->model = $note;
    }

    public function get()
    {
        return $this->model->get();
    }

    public function detail($id)
    {
        return $this->model->detail($id);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getUserNotifications($userId, Request $request)
    {
        $data = $request->validated();
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $status = $data['status'] ?? null;
        if ($status) {
            return $this->getByQueryBuilder(['user_id' => $userId, 'status' => $status])
                ->paginate($limit, ['*'], 'page', $page);
        } else {
            return $this->getByQueryBuilder(['user_id' => $userId])
                ->paginate($limit, ['*'], 'page', $page);
        }
    }
}
