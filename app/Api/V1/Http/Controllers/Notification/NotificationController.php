<?php

namespace App\Api\V1\Http\Controllers\Notification;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\Notification\NotificationServiceInterface;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Requests\Paginate\PaginateRequest;
use App\Api\V1\Http\Resources\Notification\NotificationResource;
use App\Api\V1\Http\Resources\Notification\NotificationDetailResource;
use App\Api\V1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Support\Response;
use App\Enums\Notification\NotificationStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Thông báo
 */

class NotificationController extends Controller
{
    use Response, AuthService;

    protected UserRepositoryInterface $userRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface         $userRepository,
        NotificationServiceInterface $service,

    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->service = $service;
    }
    /**
     * Chi tiết thông báo
     *
     * Lấy chi tiết thông báo.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @pathParam id integer required
     * id thông báo. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "id": 4,
     *          "title": "TEST GỬI THÔNG BÁO",
     *          "message": "TEST GỬI THÔNG BÁO",
     *          "status": "Đã đọc",
     *          "created_at": "2024-10-30T11:22:03+07:00"
     *       }
     * }

     */
    public function detail($id): JsonResponse
    {
        $note = $this->repository->find($id);
        if ($note) {
            $note->markAsRead();
        }

        if (!$note) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy'], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new NotificationDetailResource($note),
        ]);
    }
    /**
     * Danh sách thông báo của người dùng
     *
     * Lấy danh sách thông báo của người dùng.
     *
     * @queryParam page integer
     * Số lượng bài viết muốn lấy mỗi chuyên mục. Example: 1
     *
     * @queryParam limit integer
     * Số lượng bài viết muốn lấy mỗi chuyên mục. Example: 4
     *
     * @queryParam status integer
     * Trạng thái thông báo. Example: 1
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 4,
     *              "title": "TEST GỬI THÔNG BÁO",
     *              "message": "TEST GỬI THÔNG BÁO",
     *              "status": "Đã đọc",
     *              "created_at": "2024-10-30T11:22:03+07:00"
     *           }
     *      ]
     * }

     */
    public function getUserNotifications(PaginateRequest $request): JsonResponse
    {
        $userId = auth('user')->user()->id;
        $notifications = $this->repository->getUserNotifications($userId, $request);

        return $this->jsonResponseSuccess(new NotificationResource($notifications));
    }

    /**
     * Đọc tất cả thông báo
     *
     * Dùng để đọc tất cả thông báo.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }

     */
    public function updateAllStatusRead(): JsonResponse
    {
        $userId = auth('user')->user()->id;
        $criteria = [
            'status' => NotificationStatus::NOT_READ,
            'user_id' => $userId,
        ];
        $notifications = $this->repository->getBy($criteria);
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return $this->jsonResponseSuccess(null);
    }

    /**
     * Cập nhật device_token.
     *
     * API này dùng để cập nhật device_token cho người dùng
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam device_token string required
     * Device Token. Example: fKyIN9ACdV873pS0aOUrSi:APA91bHED_QSz3XnUSMQst7jtTQPLZEwkEn-CbTLnYWCxuwg-2xx2xEZO1fpltclMG0zVxKdkOMMzlx0taaxGu6HiWfYLVFJkVWeaMQRAnGsL65-O5OTbIIfs1j3ntpNakLQhc4KtVSB
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     *
     * @response 500 {
     *      "status": 500,
     *      "message": "Error updating device token: ..."
     * }
     *
     * @return JsonResponse
     */
    public function updateDeviceToken(Request $request)
    {
        return $this->service->updateDeviceToken($request);
    }

    /**
     * Xoá thông báo.
     *
     * API này dùng để xoá thông báo
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam device_token string required
     * Device Token. Example: fKyIN9ACdV873pS0aOUrSi:APA91bHED_QSz3XnUSMQst7jtTQPLZEwkEn-CbTLnYWCxuwg-2xx2xEZO1fpltclMG0zVxKdkOMMzlx0taaxGu6HiWfYLVFJkVWeaMQRAnGsL65-O5OTbIIfs1j3ntpNakLQhc4KtVSB
     *
     * @pathParam id integer required
     * id thông báo. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công"
     * }
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $user = $this->getCurrentUser();
        $notification = $this->repository->find($id);
        if ($notification && $user->id == $notification->user_id) {
            $result = $this->service->delete($id);
            if ($result) {
                return $this->jsonResponseSuccess(null);
            }
        }
        return $this->jsonResponseError();
    }

    /**
     * Xoá tất cả thông báo.
     *
     * API này dùng để xoá tất cả thông báo
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công"
     * }
     *
     * @return JsonResponse
     */
    public function deleteAll()
    {
        $user = $this->getCurrentUser();
        $notifications = $this->repository->getBy(['user_id' => $user->id]);
        foreach ($notifications as $notification) {
            $this->service->delete($notification->id);
        }
        return $this->jsonResponseSuccess(null);
    }
}
