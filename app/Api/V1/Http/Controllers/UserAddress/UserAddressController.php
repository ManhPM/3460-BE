<?php

namespace App\Api\V1\Http\Controllers\UserAddress;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\UserAddress\UserAddressRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Requests\UserAddress\UserAddressRequest;
use App\Api\V1\Support\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Địa chỉ
 */

class UserAddressController extends Controller
{
    use Response, AuthService;

    public function __construct(
        UserAddressRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }
    /**
     * Danh sách địa chỉ của người dùng
     *
     * Lấy danh sách địa chỉ của người dùng.
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
     *              "id": 6,
     *              "name": "Phạm Minh Mạnh",
     *              "phone": "0964989312",
     *              "email": "example@email.com",
     *              "address": "D2/084B Nam Sơn Quang Trung Thống Nhất Đồng Nai",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 1
     *          },
     *          {
     *              "id": 4,
     *              "name": "NGUYỄN PHÚC NHÂN",
     *              "phone": "0987654321",
     *              "email": "example@email.com",
     *              "address": "D2/084B Nam Sơn Quang Trung Thống Nhất Đồng Nai",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 0
     *          },
     *          {
     *              "id": 5,
     *              "name": "PHẠM MINH MẠNH",
     *              "phone": "0961592551",
     *              "email": "example@email.com",
     *              "address": "998/42/15 Quang Trung",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 0
     *          }
     *      ]
     * }
     */
    public function index(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if ($user) {
            return $this->jsonResponseSuccess($user->addresses);
        }
        return $this->jsonResponseError();
    }

    /**
     * Chi tiết địa chỉ của người dùng
     *
     * Lấy chi tiết địa chỉ của người dùng.
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
     *      "data": {
     *              "id": 5,
     *              "name": "PHẠM MINH MẠNH",
     *              "phone": "0961592551",
     *              "email": "example@email.com",
     *              "address": "998/42/15 Quang Trung",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 0
     *      }
     * }
     */
    public function show($id): JsonResponse
    {
        $instance = $this->repository->findOrFail($id);
        if ($instance) {
            return $this->jsonResponseSuccess($instance);
        }
        return $this->jsonResponseError();
    }

    /**
     * Thêm địa chỉ giao hàng
     *
     * Thêm địa chỉ cho người dùng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam name string required
     * Họ và tên người nhận hàng. Example: Nguyen Van A
     *
     * @bodyParam phone string required
     * Số điện thoại giao hàng. Example: 0999999999
     *
     * @bodyParam address string required
     * Địa chỉ chi tiết. Example: 998/42/15 Quang Trung
     *
     * @bodyParam province_id integer required
     * Id tỉnh/thành. Example: 1
     *
     * @bodyParam ward_id integer required
     * Id Thành phố/Khu vực. Example: 1
     *
     * @bodyParam is_default integer required
     * Trạng thái mặc định. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *              "id": 5,
     *              "name": "PHẠM MINH MẠNH",
     *              "phone": "0961592551",
     *              "email": "example@email.com",
     *              "address": "998/42/15 Quang Trung",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 0
     *      }
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công."
     * }
     */
    public function store(UserAddressRequest $request)
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();

        if ($user) {
            $data['user_id'] = $user->id;

            // Nếu user chưa có địa chỉ nào, đặt địa chỉ này làm mặc định
            if (!$user->addresses()->exists()) {
                $data['is_default'] = 1;
            } elseif (!empty($data['is_default'])) {
                // Nếu user đã có địa chỉ mặc định và địa chỉ mới có `is_default = 1`, cập nhật tất cả về 0
                $user->addresses()->update(['is_default' => 0]);
            }

            // Tạo địa chỉ mới
            $result = $this->repository->create($data);

            if ($result) {
                return $this->jsonResponseSuccess($result);
            }
        }

        return $this->jsonResponseError();
    }


    /**
     * Cập nhật địa chỉ giao hàng
     *
     * Cập nhật địa chỉ cho người dùng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id integer required
     * Id của địa chỉ. Example: 1
     *
     * @bodyParam name string required
     * Họ và tên người nhận hàng. Example: Nguyen Van A
     *
     * @bodyParam phone string required
     * Số điện thoại giao hàng. Example: 0999999999
     *
     * @bodyParam email string nullable
     * Email người nhận. Example: example@email.com
     *
     * @bodyParam address string required
     * Địa chỉ chi tiết. Example: 998/42/15 Quang Trung
     *
     * @bodyParam province_id integer required
     * Id tỉnh/thành. Example: 1
     *
     * @bodyParam ward_id integer required
     * Id Thành phố/Khu vực. Example: 1
     *
     * @bodyParam is_default integer required
     * Trạng thái mặc định. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *              "id": 5,
     *              "name": "PHẠM MINH MẠNH",
     *              "phone": "0961592551",
     *              "email": "example@email.com",
     *              "address": "998/42/15 Quang Trung",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 0
     *      }
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công."
     * }
     */
    public function update(UserAddressRequest $request)
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();

        // Kiểm tra xem địa chỉ có thuộc về user không
        if (!$user || !$user->addresses->contains('id', $data['id'])) {
            return $this->jsonResponseError();
        }

        // Nếu cập nhật `is_default = 1`, cần bỏ trạng thái mặc định của các địa chỉ khác
        if (!empty($data['is_default'])) {
            $user->addresses()->update(['is_default' => 0]);
        }

        // Cập nhật địa chỉ
        $result = $this->repository->update($data['id'], $data);

        return $result ? $this->jsonResponseSuccess($result) : $this->jsonResponseError();
    }


    /**
     * Xoá địa chỉ giao hàng
     *
     * Xoá địa chỉ cho người dùng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id integer required
     * Id của địa chỉ. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *              "id": 5,
     *              "name": "PHẠM MINH MẠNH",
     *              "phone": "0961592551",
     *              "email": "example@email.com",
     *              "address": "998/42/15 Quang Trung",
     *              "province": {
     *                  "id": 50,
     *                  "name": "Thành phố Hồ Chí Minh"
     *              },
     *              "ward": {
     *                  "id": 8716,
     *                  "name": "Phường 8"
     *              },
     *              "is_default": 0
     *      }
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công."
     * }
     */
    public function delete($id)
    {
        $user = $this->getCurrentUser();
        $instance = $this->repository->findOrFail($id);

        if (!$user || !$user->addresses->contains('id', $id)) {
            return $this->jsonResponseError();
        }

        $this->repository->delete($id);
        return $this->jsonResponseSuccess($instance);
    }

    /**
     * Chọn địa chỉ giao hàng mặc định
     *
     * Chọn địa chỉ giao hàng mặc định cho người dùng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id integer required
     * Id của địa chỉ. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công."
     * }
     */
    public function setDefault($id)
    {
        $user = $this->getCurrentUser();

        if (!$user || !$user->addresses->contains('id', $id)) {
            return $this->jsonResponseError();
        }

        foreach ($user->addresses as $address) {
            $address->is_default = $address->id == $id ? 1 : 0;
            $address->save();
        }

        return $this->jsonResponseSuccess(null);
    }
}
