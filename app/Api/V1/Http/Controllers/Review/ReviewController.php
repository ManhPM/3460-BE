<?php

namespace App\Api\V1\Http\Controllers\Review;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\Review\ReviewServiceInterface;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Requests\Review\ReviewRequest;
use App\Api\V1\Repositories\Review\ReviewRepositoryInterface;
use App\Api\V1\Http\Resources\Review\ReviewResource;
use Illuminate\Http\Request;

/**
 * @group Đánh giá sản phẩm
 */

class ReviewController extends Controller
{
    use AuthService;
    public function __construct(
        ReviewRepositoryInterface $repository,
        ReviewServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Danh sách đánh giá sản phẩm
     *
     * Lấy danh sách đánh giá của một sản phẩm.
     *
     * @queryParam product_id integer required
     * id sản phẩm. Example: 20
     *
     * @queryParam limit integer
     * Giới hạn số đánh giá trên mỗi trang. Example: 10
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 10,
     *              "fullname": "Tran Van A",
     *              "avatar": "http://domain.com/public/assets/images/default-image.png",
     *              "content": "Sản phẩm rất tốt",
     *              "rating": 5,
     *              "images": [],
     *              "created_at": "2024-01-15T10:30:00.000000Z"
     *          }
     *      ]
     * }
     */
    public function index($productId)
    {
        $limit = request()->input('limit', 10);

        $reviews = $this->repository->getReviewsByProductId($productId, $limit);
        $reviews = new ReviewResource($reviews);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $reviews
        ], 200);
    }

    /**
     * Tạo đánh giá
     *
     * Tạo đánh giá cho một sản phẩm.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam order_detail_id integer required
     * id chi tiết đơn hàng. Example: 20
     *
     * @bodyParam rating integer required
     * Số sao đánh giá. Example: 5
     *
     * @bodyParam content string
     * Nội dung đánh giá. Example: content
     *
     * @bodyParam images array
     * Mảng ảnh đánh giá (base64). Example: []
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *       "data": {
     *            "id": 10,
     *            "fullname": "Tran Van A",
     *            "avatar": "http://domain.com/public/assets/images/default-image.png",
     *            "content": "content",
     *            "rating": 5
     *      }
     * }
     */

    public function store(ReviewRequest $request)
    {
        $response = $this->service->store($request);
        if ($response) {
            return response()->json([
                'status' => 200,
                'message' => __('review.create_success'),
            ], 200);
        }
        return response()->json([
            'status' => 500,
            'message' => __('fail'),
        ], 500);
    }
}
