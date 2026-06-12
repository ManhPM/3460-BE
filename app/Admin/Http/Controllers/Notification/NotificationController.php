<?php

namespace App\Admin\Http\Controllers\Notification;

use App\Admin\DataTables\Notification\NotificationDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Notification\NotificationRequest;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\Notification\NotificationServiceInterface;
use App\Admin\Traits\AuthService;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use AuthService;
    protected $userRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        NotificationServiceInterface    $service
    ) {

        parent::__construct();

        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->service = $service;
    }

    protected function getActionMultiple(): array
    {
        return [
            'delete' => __('Xoá vĩnh viễn')
        ];
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.notifications.index',
            'indexView' => 'admin.dashboard.notification',
            'create' => 'admin.notifications.create',
            'edit' => 'admin.notifications.edit',
            'show' => 'admin.dashboard.show-notification',
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.notification.index',
            'create' => 'admin.notification.create',
            'edit' => 'admin.notification.edit',
        ];
    }

    public function index(NotificationDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách thông báo')),
            'actionMultiple' => $this->getActionMultiple()
        ]);
    }

    public function actionMultipleRecord(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            switch ($data['action']) {
                case 'delete':
                    foreach ($data['id'] as $value) {
                        $this->repository->delete($value);
                    }
                default:
            }
            DB::commit();
            return back()->with('success', __('success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Action multiple record failed: ' . $th->getMessage);
            return back()->with('error', __('fail'));
        }
    }

    public function getAllNotificationAdmin()
    {
        $isNewNotification = true;
        $notifications = $this->repository->getBy(['admin_id' => $this->getCurrentAdminId(), 'status' => NotificationStatus::NOT_READ, 'read_at' => null]);
        if (!isset($notifications[0])) {
            $isNewNotification = false;
            $notifications = $this->repository->getBy(['admin_id' => $this->getCurrentAdminId()], [], 5);
        }
        return response()->json([
            'status' => 200,
            'data' => $notifications,
            'count' => $isNewNotification ? count($notifications) : 0
        ]);
    }

    public function show($id): View|Application
    {
        $response = $this->repository->findOrFail($id);
        if ($response) {
            $response->markAsRead();
        }
        return view(
            $this->view['show'],
            [
                'notification' => $response,
                'breadcrumbs' => $this->crums->add(__('Chi tiết thông báo'))
            ],
        );
    }

    public function readAllNotification()
    {
        $notifications = $this->repository->getBy(['admin_id' => $this->getCurrentAdminId()]);
        foreach ($notifications as $notification) {
            $notification->update(['status' => NotificationStatus::READ, 'read_at' => now()]);
        }
        return back()->with('success', __('success'));
    }


    public function create(): View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách thông báo'), route($this->route['index']))->add(__('add')),
            [
                'types' => NotificationType::asSelectArray(),
                'status' => NotificationStatus::asSelectArray(),
            ]
        );
    }

    public function edit($id): View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách thông báo'), route($this->route['index']))->add(__('edit')),
            [
                'notification' => $this->repository->findOrFail($id),
                'types' => NotificationType::asSelectArray(),
                'status' => NotificationStatus::asSelectArray(),
            ]
        );
    }


    public function store(NotificationRequest $request)
    {
        $response = $this->service->store($request);
        if ($response) {
            return redirect()->route($this->route['index'])->with('success', __('success'));
        }
        return redirect()->route($this->route['create'])->with('error', __('fail'));
    }

    public function update(NotificationRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }


    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }

    public function updateDeviceToken(Request $request)
    {
        return $this->service->updateDeviceToken($request);
    }


    public function getAllNotificationAdminView()
    {
        $notifications = $this->repository->paginateAdmin($this->getCurrentAdminId(), 3);
        return view($this->view['indexView'], [
            'notifications' => $notifications,
            'breadcrumbs' => $this->crums->add(__('notification_list')),
        ]);
    }
}
