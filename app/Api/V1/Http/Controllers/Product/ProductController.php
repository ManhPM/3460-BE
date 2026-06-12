<?php

namespace App\Api\V1\Http\Controllers\Product;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use App\Api\V1\Repositories\Product\ProductRepositoryInterface;
use App\Api\V1\Http\Resources\Product\{AllProductResource, AllProductBestSellingResource, FlashSaleResource, ShowProductResource};
use App\Api\V1\Http\Requests\Product\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @group Sản phẩm
 */

class ProductController extends Controller
{
    protected FlashSaleRepositoryInterface $flashSaleRepository;
    public function __construct(
        ProductRepositoryInterface $repository,
        FlashSaleRepositoryInterface $flashSaleRepository,
    ) {
        $this->repository = $repository;
        $this->flashSaleRepository = $flashSaleRepository;
    }

    /**
     * Danh sách sản phẩm
     *
     * Lấy danh sách sản phẩm.
     *
     * @queryParam keyword string Từ khóa tên sản phẩm.
     * Example: ipad
     *
     * @queryParam limit integer Giới hạn số sản phẩm trên mỗi trang.
     * Example: 10
     *
     * @queryParam page integer Số trang hiện tại.
     * Example: 1
     *
     * @queryParam min_product_price string Giá sản phẩm tối thiểu.
     * Example: 100000
     *
     * @queryParam max_product_price string Giá sản phẩm tối đa.
     * Example: 500000
     *
     * @queryParam category_ids array Id của danh mục sản phẩm.
     * Example: [1, 2, 3]
     *
     * @queryParam sort string Tiêu chí sắp xếp.
     * Example: asc
     *
     * Giải thích `sort`:
     * - `asc`: Sắp xếp theo giá trị tăng dần.
     * - `desc`: Sắp xếp theo giá trị giảm dần.
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *               "id": 10,
     *               "name": "Iphone 14",
     *               "is_flash_sale": true,
     *               "flashsale_price": 20000,
     *               "flashsale_sold": 0,
     *               "flashsale_qty": 100,
     *               "avatar": "http://localhost/topzone/public/assets/images/default-image.png",
     *               "price": 20900,
     *               "promotion_price": 10000,
     *               "review": [
     *                  {
     *                      "id": 7,
     *                      "user_id": 1,
     *                      "rating": 5,
     *                      "content": "Hài lòng",
     *                      "product_id": 16,
     *                      "created_at": "2024-11-01T08:06:30.000000Z",
     *                      "updated_at": "2024-11-01T08:06:30.000000Z",
     *                      "order_id": null
     *                  }
     *              ],
     *              "avg_rating": 5
     *           }
     *      ]
     * }
     */
    public function index(ProductRequest $request)
    {
        $request->validated();

        $filter = [
            'min_product_price' => $request->input('min_product_price'),
            'max_product_price' => $request->input('max_product_price'),
            'category_ids' => $request->input('category_ids'),
            'keyword' => $request->input('keyword', ''),
            'limit' => $request->input('limit', 8)
        ];

        $products = $this->repository->getProductsWithRelations($filter, [], $request->input('sort'));

        $products = new AllProductResource($products);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $products
        ]);
    }

    /**
     * Chương trình flash sale
     *
     * Lấy ra chương trình flash sale cũng như các sản phẩm đang có trong chương trình.
     *
     * @headersParam X-TOKEN-ACCESS string required
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam keywords string
     * Từ khóa tên sản phẩm. Example: ipad
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "SALE15",
     *              "start_time": "19-09-2024 12:40",
     *              "end_time": "27-11-2024 12:40",
     *              "products": [
     *                  {
     *                      "id": 18,
     *                      "name": "CELL PHONE Z",
     *                      "slug": "cell-phone-z",
     *                      "is_flash_sale": true,
     *                      "avatar": "http://localhost:8080/2906-BahaGroup/userfiles/files/d1.jpg",
     *                      "flashsale_price": 4000000,
     *                      "flashsale_sold": 0,
     *                      "flashsale_qty": 100,
     *                      "min_promotion_price": 4500000,
     *                      "min_price": 5000000
     *                  }
     *              ]
     *           }
     *      ]
     * }
     */
    public function saleLimited()
    {
        $flashSale = $this->flashSaleRepository->getCurrentFlashSale();
        if ($flashSale) {
            return response()->json([
                'status' => 200,
                'message' => __('flash_sale.is_active'),
                'data' => new FlashSaleResource($flashSale)
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => __('flash_sale.not_active'),
                'data' => []
            ]);
        }
    }
    /**
     * Sản phẩm gợi ý
     *
     * Lấy 6 sản phẩm gợi ý ngẫu nhiên dựa vào lượt bán và rating.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 10,
     *              "name": "Iphone 14",
     *              "avatar": "http://localhost/topzone/public/assets/images/default-image.png",
     *              "price": 20900,
     *              "promotion_price": 10000,
     *              "total_sold": 150,
     *              "avg_rating": 4.5
     *          }
     *      ]
     * }
     */
    public function suggested()
    {
        $products = $this->repository->getSuggestedProducts(6);
        $products = new AllProductBestSellingResource($products);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $products
        ]);
    }

    /**
     * Chi tiết sản phẩm
     *
     * Lấy chi tiết của sản phẩm.
     *
     * @headersParam X-TOKEN-ACCESS string required
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @pathParam id integer required
     * id sản phẩm. Example: 1
     *
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 10,
     *               "name": "Iphone 14",
     *               "slug": "iphone-14",
     *               "is_flash_sale": true,
     *               "flashsale_price": 20000,
     *               "flashsale_sold": 0,
     *               "flashsale_qty": 100,
     *               "avatar": "http://localhost/topzone/public/assets/images/default-image.png",
     *               "price": 20900,
     *               "promotion_price": 10000,
     *               "review": [
     *                  {
     *                      "id": 7,
     *                      "user_id": 1,
     *                      "rating": 5,
     *                      "content": "Hài lòng",
     *                      "product_id": 16,
     *                      "created_at": "2024-11-01T08:06:30.000000Z",
     *                      "updated_at": "2024-11-01T08:06:30.000000Z",
     *                      "order_id": null
     *                  }
     *               ],
     *              "attributes": [
     *                  {
     *                      "name": "Màu sắc",
     *                      "values": [
     *                          "Màu đen",
     *                          "Màu trắng"
     *                      ]
     *                  },
     *                  {
     *                      "name": "Kích thước",
     *                      "values": [
     *                          "Nhỏ",
     *                          "Vừa"
     *                      ]
     *                  }
     *              ],
     *              "variants": [
     *                  {
     *                      "variant_id": 25,
     *                      "price": 4500000,
     *                      "promotion_price": 4000000,
     *                      "flashsale_price": 3000000,
     *                      "qty": 100,
     *                      "image": "http://localhost:8080/CoreBanHang/public/assets/images/default-image.png",
     *                      "attributes": {
     *                          "color": "Màu đen",
     *                          "size": "Nhỏ"
     *                      }
     *                  }
     *              ],
     *               "avg_rating": 5
     *           }
     *      ]
     * }
     */
    public function show($id, Request $request)
    {
        $this->handleAffiliateCode($request, $id);
        try {
            $product = $this->repository->findOrFailWithRelations($id, [
                'productAttributes',
                'productVariations.attribute_variations',
                'reviews.user'
            ]);
            $product = new ShowProductResource($product);
            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $product
            ]);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'status' => 404,
                'message' => __('product.not_exists')
            ], 404);
        }
    }

    /**
     * Sản phẩm liên quan
     *
     * Lấy tối đa 6 sản phẩm liên quan từ cùng category với sản phẩm hiện tại.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @pathParam id integer required
     * id sản phẩm. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 10,
     *              "name": "Iphone 14",
     *              "avatar": "http://localhost/topzone/public/assets/images/default-image.png",
     *              "price": 20900,
     *              "promotion_price": 10000,
     *              "total_sold": 150,
     *              "avg_rating": 4.5
     *          }
     *      ]
     * }
     */
    public function related($id)
    {
        $products = $this->repository->getRelatedProducts($id, 6);
        $products = new AllProductBestSellingResource($products);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $products
        ]);
    }

    /**
     * Trang redirect để mở app từ web
     *
     * Hiển thị trang HTML để tự động mở ứng dụng với deep link
     *
     * @param int $id ID sản phẩm
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function redirect($id, Request $request)
    {
        $affiliateCode = $request->input('affiliate_code');

        return view('product-redirect', [
            'productId' => $id,
            'affiliateCode' => $affiliateCode
        ]);
    }

    function handleAffiliateCode(Request $request, $id)
    {
        if (!$request->input('affiliate_code')) {
            return;
        }

        $affiliate_code = $request->input('affiliate_code');
        $currentAffiliateCode = auth()->check() ? auth()->user()->affiliate_code : null;

        // Nếu user đã đăng nhập và affiliate_code trùng với của user, không làm gì
        if (auth()->check() && $affiliate_code === $currentAffiliateCode) {
            return;
        }

        // Lấy identifier cho cache key (user_id nếu đã login, hoặc hash IP+User-Agent nếu chưa login)
        $identifier = $this->getAffiliateIdentifier($request);
        $cacheKey = "affiliate_{$identifier}_{$id}";

        // Kiểm tra cache hiện tại
        $cachedData = Cache::get($cacheKey);

        // Thời gian hết hạn: 7 ngày từ bây giờ
        $expiresAt = now()->addDays(7);

        // Nếu có cache và affiliate_code khác, hoặc đã hết hạn, cập nhật lại
        if (!$cachedData || $cachedData['affiliate_code'] !== $affiliate_code || ($cachedData['expires_at'] ?? null) < now()->timestamp) {
            Cache::put($cacheKey, [
                'affiliate_code' => $affiliate_code,
                'product_id' => $id,
                'expires_at' => $expiresAt->timestamp,
            ], $expiresAt);
        }
    }

    /**
     * Lấy identifier để tạo cache key cho affiliate
     * Nếu user đã login: dùng user_id
     * Nếu chưa login: dùng hash của IP + User-Agent
     */
    private function getAffiliateIdentifier(Request $request): string
    {
        if (auth()->check()) {
            return 'user_' . auth()->id();
        }

        // Tạo identifier từ IP và User-Agent cho guest
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        return 'guest_' . md5($ip . $userAgent);
    }
}
