<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Admin\Traits\AuthService;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Support\Response;
use App\Enums\Notification\NotificationStatus;

class NotificationController extends Controller
{
    use AuthService, Response;

    protected $repository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'user.notifications.index',
            'show' => 'user.notifications.detail',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'user.notification.index',
        ];
    }

    public function index()
    {
        $notifications = $this->repository->getByUserIdAndPaginate($this->getCurrentUserId());
        return view($this->view['index'], [
            'notifications' => $notifications,
            'breadcrumbs' => $this->crums->add(__('Danh sách thông báo'))->getBreadcrumbs()
        ]);
    }

    public function readAll()
    {
        $notifications = $this->repository->getBy(['user_id' => $this->getCurrentUserId(), 'status' => NotificationStatus::NOT_READ]);
        foreach ($notifications as $notification) {
            $notification->update([
                'status' => NotificationStatus::READ,
                'read_at' => now(),
            ]);
        }
        return back()->with('success', __('Đã đọc tất cả thông báo.'));
    }

    public function show($id)
    {
        $notification = $this->repository->findOrFail($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return view($this->view['show'], [
            'notification' => $notification,
            'breadcrumbs' => $this->crums->add(__('Danh sách thông báo'), route($this->route['index']))->add(__('Chi tiết thông báo'))->getBreadcrumbs()
        ]);
    }

    public function delete($id)
    {
        $result = $this->repository->delete($id);
        if ($result) {
            return back()->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }
}
