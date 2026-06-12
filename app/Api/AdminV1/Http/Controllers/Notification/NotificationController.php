<?php

namespace App\Api\AdminV1\Http\Controllers\Notification;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Notification\NotificationRequest;
use App\Api\AdminV1\Http\Resources\Notification\NotificationResource;
use App\Api\AdminV1\Http\Resources\Notification\NotificationCollection;
use App\Api\AdminV1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\AdminV1\Services\Notification\NotificationService;

class NotificationController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        NotificationRepositoryInterface $repository,
        NotificationService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $notifications = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new NotificationCollection($notifications),
        ]);
    }

    public function show(int $id)
    {
        $notification = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new NotificationResource($notification->load('user'))
        ]);
    }

    public function store(NotificationRequest $request)
    {
        $validated = $request->validated();
        $notifications = $this->service->create($validated);

        // Nếu là array (gửi cho nhiều users), trả về array of resources
        if (is_array($notifications)) {
            return response()->json([
                'status' => 201,
                'message' => __('Tạo thông báo thành công.'),
                'data' => array_map(function ($notification) {
                    return new NotificationResource($notification->load('user'));
                }, $notifications),
            ], 201);
        }

        // Nếu là single notification, trả về resource
        return response()->json([
            'status' => 201,
            'message' => __('Tạo thông báo thành công.'),
            'data' => new NotificationResource($notifications->load('user')),
        ], 201);
    }

    public function update(NotificationRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $notification = $this->service->update($id, $request->validated());
                return new NotificationResource($notification->load('user'));
            },
            __('notification.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('notification.deleted_success')
        );
    }
}
