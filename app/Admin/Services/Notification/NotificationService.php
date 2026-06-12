<?php

namespace App\Admin\Services\Notification;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Enums\Notification\NotificationType;
use App\Traits\NotifiesViaFirebase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationService implements NotificationServiceInterface
{
    use AuthService, NotifiesViaFirebase;

    protected $data;

    protected $repository;
    private UserRepositoryInterface $userRepository;
    private AdminRepositoryInterface $adminRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface        $userRepository,
        AdminRepositoryInterface        $adminRepository,
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;
    }

    public function updateDeviceToken($request): JsonResponse
    {
        try {
            $data = $request->validate([
                'device_token' => 'required|string'
            ]);
            $admin = $this->getCurrentAdmin();
            if ($admin) {
                if ($admin->device_token == null || $admin->device_token != $data['device_token']) {
                    $this->adminRepository->update($admin->id, [
                        'device_token' => $data['device_token'],
                        'device_token_updated_at' => now()
                    ]);
                    return response()->json(['message' => 'Update device token success.'], 200);
                } else {
                    return response()->json(['message' => 'Device token is up to date.'], 200);
                }
            } else {
                $user = $this->getCurrentUser();
                if ($user->device_token == null || $user->device_token != $data['device_token']) {
                    $this->userRepository->update($user->id, [
                        'device_token' => $data['device_token'],
                    ]);
                    return response()->json(['status' => 200, 'message' => 'Update device token success.'], 200);
                } else {
                    return response()->json(['status' => 200, 'message' => 'Device token is up to date.'], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to update token.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        try {
            DB::beginTransaction();

            $notifications = [];
            $deviceTokens = [];

            if (empty($this->data['user_id'])) {
                // Send to all users
                $users = $this->userRepository->getAll();
                foreach ($users as $user) {
                    $this->data['user_id'] = $user->id;
                    $notification = $this->repository->create($this->data);
                    $notifications[] = $notification;
                    if ($user->device_token) {
                        $deviceTokens[] = $user->device_token;
                    }
                }
            } else {
                // Handle specific user notifications
                foreach ($this->data['user_id'] as $userId) {
                    $this->data['user_id'] = $userId;
                    $user = $this->userRepository->findOrFail($userId);
                    $notification = $this->repository->create($this->data);
                    $notifications[] = $notification;
                    if ($user->device_token) {
                        $deviceTokens[] = $user->device_token;
                    }
                }
            }

            // Send firebase notification once for all device tokens
            if (!empty($notifications) && !empty($deviceTokens)) {
                $firstNotification = $notifications[0];
                $this->sendFirebaseNotification(
                    $deviceTokens,
                    null,
                    $firstNotification->title,
                    $firstNotification->short_message,
                    $firstNotification->id
                );
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function update(Request $request): object|bool
    {

        $this->data = $request->validated();

        return $this->repository->update($this->data['id'], $this->data);
    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }
}
