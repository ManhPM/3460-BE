<?php

namespace App\Api\AdminV1\Services\Notification;

use App\Api\AdminV1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\AdminV1\Repositories\User\UserRepositoryInterface;
use App\Traits\NotifiesViaFirebase;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    use NotifiesViaFirebase;

    protected $repository;
    protected $userRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $notifications = [];
            $deviceTokens = [];

            // Nếu không có user_id, lấy tất cả users và tạo notification cho từng user
            if (empty($data['user_id']) || $data['user_id'] === null) {
                $users = $this->userRepository->getAll();

                foreach ($users as $user) {
                    $notificationData = $data;
                    $notificationData['user_id'] = $user->id;
                    $notification = $this->repository->create($notificationData);
                    $notifications[] = $notification;

                    // Thu thập device token nếu có
                    if ($user->device_token) {
                        $deviceTokens[] = $user->device_token;
                    }
                }
            } else {
                // Nếu có user_id, tạo notification cho user đó
                $user = $this->userRepository->findOrFail($data['user_id']);
                $notification = $this->repository->create($data);
                $notifications[] = $notification;

                // Thu thập device token nếu có
                if ($user->device_token) {
                    $deviceTokens[] = $user->device_token;
                }
            }

            // Gửi Firebase push notification cho tất cả device tokens
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

            // Trả về single notification hoặc array
            return count($notifications) === 1 ? $notifications[0] : $notifications;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
