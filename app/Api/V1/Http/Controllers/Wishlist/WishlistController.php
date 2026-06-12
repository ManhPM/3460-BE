<?php

namespace App\Api\V1\Http\Controllers\Wishlist;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Wishlist\WishlistRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Resources\Wishlist\AllWishlistResource;
use App\Api\V1\Support\Response;

/**
 * @group Danh sách yêu thích
 */

class WishlistController extends Controller
{
    use AuthService, Response;
    public function __construct(
        WishlistRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * Danh sách yêu thích
     *
     * Lấy danh sách yêu thích.
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @headersParam X-TOKEN-ACCESS string required
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *               "id": 10,
     *               "fullname": "Tran Van A",
     *               "avatar": "http://domain.com/public/assets/images/default-image.png",
     *               "content": "content",
     *               "rating": 5
     *           }
     *      ]
     * }

     */

    public function index()
    {
        $user = $this->getCurrentUser();
        $wishlists = $this->repository->getBy(['user_id' => $user->id]);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AllWishlistResource($wishlists)
        ], 200);
    }

    /**
     * Toggle sản phẩm trong danh sách yêu thích
     *
     * Toggle sản phẩm trong danh sách yêu thích.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @pathParam id string required
     * Id của sản phẩm. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "is_wishlist": 1
     *      }
     * }
     */
    public function toggle($id)
    {
        $user = $this->getCurrentUser();
        $wishlist = $this->repository->getBy(['product_id' => $id, 'user_id' => $user->id])->first();
        if ($wishlist) {
            $wishlist->delete();
            return $this->jsonResponseSuccess(['is_wishlist' => 0]);
        }
        $this->repository->create(['product_id' => $id, 'user_id' => $user->id]);
        return $this->jsonResponseSuccess(['is_wishlist' => 1]);
    }
}
