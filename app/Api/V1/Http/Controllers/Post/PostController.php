<?php

namespace App\Api\V1\Http\Controllers\Post;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Api\V1\Http\Requests\Post\PostRequest;
use App\Api\V1\Http\Resources\Post\{AllPostResource, ShowPostResource};
use App\Api\V1\Repositories\Post\PostRepositoryInterface;

/**
 * @group Bài viết
 */

class PostController extends Controller
{
    protected $postCategoryRepository;
    public function __construct(
        PostRepositoryInterface $repository,
        PostCategoryRepositoryInterface $postCategoryRepository,
    ) {
        $this->repository = $repository;
        $this->postCategoryRepository = $postCategoryRepository;
    }
    /**
     * DS bài viết
     *
     * Lấy danh sách bài viết.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     *
     * @queryParam limit integer
     * Số lượng bài viết trong 1 trang. limit > 0. Example: 1
     *
     * @queryParam category_id integer
     * Id chuyên mục bài viết muốn lọc. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 4,
     *               "title": "Hé lộ ios 17 sắp ra mắt",
     *               "slug": "he-lo-ios-17-sap-ra-mat",
     *               "image": "http://localhost/topzone/public/uploads/images/accnhi96141892532044.png",
     *               "is_featured": true,
     *               "excerpt": "Hé lộ ios 17 sắp ra mắt",
     *               "posted_at": "2023-04-19 10:12:57"
     *           }
     *      ]
     * }

     */
    public function index(PostRequest $request)
    {
        $data = $request->validated();

        $posts = $this->repository->paginate(...$data);
        $posts = new AllPostResource($posts);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $posts
        ]);
    }
    /**
     * DS bài viết nổi bật
     *
     * Lấy danh sách bài viết nổi bật.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     *
     * @queryParam limit integer
     * Số lượng bài viết trong 1 trang, limit > 0. Example: 1
     *
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 4,
     *               "title": "Hé lộ ios 17 sắp ra mắt",
     *               "slug": "he-lo-ios-17-sap-ra-mat",
     *               "image": "http://localhost/topzone/public/uploads/images/accnhi96141892532044.png",
     *               "is_featured": true,
     *               "excerpt": "Hé lộ ios 17 sắp ra mắt",
     *               "posted_at": "2023-04-19 10:12:57"
     *           }
     *      ]
     * }

     */
    public function featured(PostRequest $request)
    {
        $data = $request->validated();

        $posts = $this->repository->getFeaturedPaginate(...$data);
        $posts = new AllPostResource($posts);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $posts
        ]);
    }
    /**
     * Chi tiết bài viết
     *
     * Lấy chi tiết bài viết.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @pathParam id integer required
     * id bài viết. Example: 1
     *
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *           "id": 4,
     *           "title": "Hé lộ ios 17 sắp ra mắt",
     *           "slug": "he-lo-ios-17-sap-ra-mat",
     *           "image": "http://localhost/topzone/public/uploads/images/1597766959764584432044.png",
     *           "is_featured": true,
     *           "excerpt": "Hé lộ ios 17 sắp ra mắt",
     *           "content": "<p>H&eacute; lộ ios 17 sắp ra mắt</p>",
     *           "posted_at": "2023-04-19 10:12:57"
     *       }
     * }

     */
    public function show($id)
    {
        $post = $this->repository->findByPublished($id);
        $post = new ShowPostResource($post);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $post
        ]);
    }
    /**
     * DS bài viết liên quan
     *
     * Lấy danh sách bài viết liên quan.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @pathParam id integer required
     * id bài viết. Example: 1
     *
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     *
     * @queryParam limit integer
     * Số lượng bài viết trong 1 trang, limit > 0. Example: 1
     *
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 4,
     *               "title": "Hé lộ ios 17 sắp ra mắt",
     *               "slug": "he-lo-ios-17-sap-ra-mat",
     *               "image": "http://localhost/topzone/public/uploads/images/accnhi96141892532044.png",
     *               "is_featured": true,
     *               "excerpt": "Hé lộ ios 17 sắp ra mắt",
     *               "posted_at": "2023-04-19 10:12:57"
     *           }
     *      ]
     * }

     */
    public function related($id, PostRequest $request)
    {

        $posts = $this->repository->getRelated($id, ...$request->validated());
        $posts = new AllPostResource($posts);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $posts
        ]);
    }
}
