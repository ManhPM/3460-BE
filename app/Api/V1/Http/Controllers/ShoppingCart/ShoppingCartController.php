<?php

namespace App\Api\V1\Http\Controllers\ShoppingCart;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Requests\ShoppingCart\ApplyDiscountCodeRequest;
use App\Api\V1\Http\Requests\ShoppingCart\BuyNowRequest;
use App\Api\V1\Http\Requests\ShoppingCart\CheckoutRequest;
use App\Api\V1\Http\Requests\ShoppingCart\DeleteShoppingCartRequest;
use App\Api\V1\Services\ShoppingCart\ShoppingCartServiceInterface;
use App\Api\V1\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use App\Api\V1\Http\Requests\ShoppingCart\CreateShoppingCartRequest;
use App\Api\V1\Http\Requests\ShoppingCart\UpdateShoppingCartRequest;
use App\Api\V1\Http\Requests\ShoppingCart\ChangeVariationShoppingCartRequest;
use App\Api\V1\Http\Resources\Order\ShowOrderResource;
use App\Api\V1\Http\Resources\ShoppingCart\ShoppingCartResource;
use App\Api\V1\Http\Resources\ShoppingCart\ShoppingCartResourceNoLogin;
use App\Api\V1\Http\Resources\Branch\BranchResource;
use App\Models\Admin;
use App\Models\AdminInventory;
use App\Traits\CalculateShippingFee;
use Illuminate\Http\Request;

/**
 * @group Giỏ hàng
 */

class ShoppingCartController extends Controller
{
    use AuthService, CalculateShippingFee;
    protected $discountRepository;
    protected $voucherRepository;
    protected $settingRepository;
    public function __construct(
        ShoppingCartRepositoryInterface $repository,
        DiscountRepositoryInterface $discountRepository,
        VoucherRepositoryInterface $voucherRepository,
        SettingRepositoryInterface $settingRepository,
        ShoppingCartServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->voucherRepository = $voucherRepository;
        $this->settingRepository = $settingRepository;
        $this->service = $service;

        $this->discountRepository = $discountRepository;
    }
    /**
     * Danh sách sản phẩm trong giỏ hàng
     *
     * Lấy danh sách sản phẩm trong giỏ hàng của user.
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
     *       "id": 7,
     *       "qty": 2,
     *       "product": {
     *           "id": 5,
     *           "name": "Iphone 16",
     *           "slug": "iphone-16-1",
     *           "is_flash_sale": true,
     *           "avatar": "/public/assets/images/default-image.png"
     *       },
     *       "product_variation": {
     *           "id": 6,
     *           "price": 50000,
     *           "promotion_price": 40000,
     *           "flashsale_price": 30000,
     *           "image": "/public/assets/images/default-image.png"
     *       }
     *   }
     *      ]
     * }

     */
    public function index()
    {
        if ($this->getCurrentUser()) {
            $shoppingCart = $this->repository->getAuthCurrent();
            $shoppingCart = new ShoppingCartResource($shoppingCart);
            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $shoppingCart
            ]);
        } else {
            $shoppingCart = session('cart', []);
            $shoppingCart = new ShoppingCartResourceNoLogin($shoppingCart);
            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $shoppingCart
            ]);
        }
    }
    /**
     * Thêm sản phẩm vào giỏ hàng
     *
     * Thêm sản phẩm vào giỏ hàng của user.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam product_id integer required
     * id sản phẩm. Example: 20
     *
     * @bodyParam variation_id
     * id biến thể sản phẩm. Example: 25
     *
     * @bodyParam qty integer required
     * Số lượng sản phẩm. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }

     */
    public function store(CreateShoppingCartRequest $request)
    {
        $response = $this->service->store($request);
        if ($response === 1) {
            return response()->json([
                'status' => 400,
                'message' => __('shopping_cart.add_product_failed_max_quantity'),
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'message' => __('shopping_cart.add_to_cart_success'),
        ], 200);
    }

    /**
     * Mua ngay
     *
     * Mua ngay một sản phẩm.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam product_id integer required
     * id sản phẩm. Example: 20
     *
     * @bodyParam variation_id
     * id biến thể sản phẩm. Example: 25
     *
     * @bodyParam qty integer required
     * Số lượng sản phẩm. Example: 1
     *
     * @bodyParam points integer
     * Số xu muốn sử dụng để giảm giá. Example: 50000
     *
     * @bodyParam discount_code string
     * Mã giảm giá. Example: SALE10
     *
     * @bodyParam voucher_shipping_id string
     * ID Voucher giảm giá vận chuyển. Example: 1
     *
     * @bodyParam voucher_product_id string
     * ID Voucher giảm giá tiền hàng. Example: 2
     *
     * @bodyParam order[payment_image] file
     * Hình ảnh chuyển khoản. Example: file.png
     *
     * @bodyParam order[payment_method] string required
     * Phương thức thanh toán. Example: "1"
     *
     * @bodyParam order[email] string required
     * Email của người đặt hàng. Example: "example@example.com"
     *
     * @bodyParam order[province_id] integer required
     * ID của tỉnh. Example: 1
     *
     * @bodyParam order[ward_id] integer required
     * ID của xã/phường. Example: 100
     *
     * @bodyParam order[fullname] string required
     * Họ tên đầy đủ của người nhận. Example: "Nguyen Van A"
     *
     * @bodyParam order[address] string required
     * Địa chỉ nhận hàng. Example: "123 Nguyen Trai, Ha Noi"
     *
     * @bodyParam order[phone] string required
     * Số điện thoại người nhận. Example: "0123456789"
     *
     * @bodyParam order[note] string
     * Ghi chú đơn hàng. Example: "Giao hàng giờ hành chính"
     *
     * @bodyParam order[bank_id] integer
     * Id của ngân hàng nếu là phương thức chuyển khoản. Example: "64"
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Đặt hàng thành công.",
     *      "order": {
     *          "id": 411,
     *          "discount_code": "SALE10",
     *          "customer_fullname": "Nguyễn Văn A",
     *          "customer_phone": "0123456789",
     *          "customer_email": "example@example.com",
     *          "shipping_address": "123 Đường ABC",
     *          "note": "Ghi chú",
     *          "customer_name_other": "Nguyễn Thị B",
     *          "customer_phone_other": "0987654321",
     *          "shipping_address_other": "456 Đường XYZ",
     *          "note_other": "Ghi chú khác",
     *          "total": 60000000,
     *          "points_discount_value": null,
     *          "voucher_shipping_code": "VOUCHER3",
     *          "voucher_shipping_discount_value": 2500,
     *          "voucher_product_code": "VOUCHER4",
     *          "voucher_product_discount_value": 50000,
     *          "discount_value": 30000,
     *          "shipping_fee": 0,
     *          "code": "HDA14147",
     *          "qr_image": null,
     *          "status": "Chờ xác nhận",
     *          "payment_status": "Chưa thanh toán",
     *          "payment_method": "Chuyển khoản ngân hàng",
     *          "payment_image": null,
     *          "created_at": "2025-01-15T17:26:30.000000Z",
     *          "province": "Tỉnh Tuyên Quang",
     *          "ward": "Xã Tân Mỹ",
     *          "order_details": [
     *              {
     *                  "id": 1,
     *                  "name": "Laptop Dell Inspiron",
     *                  "qty": 5,
     *                  "unit_price": 12000000,
     *                  "slug": "laptop-dell-inspiron",
     *                  "avatar": "http://localhost:8080/CoreBanHang/userfiles/images/laptop/Dell-Inspiron-14-Plus-7430-laptop365-2.png"
     *              }
     *          ]
     *      }
     * }
     *
     * @response 500 {
     *      "status": 500,
     *      "message": "Đặt hàng thất bại."
     * }

     */
    public function buyNow(BuyNowRequest $request)
    {
        $response = $this->service->buyNow($request);
        if ($response) {
            return response()->json([
                'status' => 200,
                'message' => __('order.create_success'),
                'order' => new ShowOrderResource($response)
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => __('order.create_failed'),
        ], 400);
    }

    /**
     * Cập nhật giỏ hàng
     *
     * Cập nhật hoặc xóa item giỏ hàng của user.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id[] integer required
     * Danh sách id item giỏ hàng. Example: 1
     *
     * @bodyParam qty[] integer required
     * Danh sách qty phải tương ứng với ds id. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     */
    public function update(UpdateShoppingCartRequest $request)
    {
        $response = $this->service->update($request);
        if ($response === true) {
            return response()->json([
                'status' => 200,
                'message' => __('shopping_cart.update_success'),
            ]);
        } else if ($response) {
            session(['cart' => $response]);
            return response()->json([
                'status' => 200,
                'message' => __('shopping_cart.update_success'),
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => __('shopping_cart.update_failed')
        ], 400);
    }
    /**
     * Xóa item giỏ hàng
     *
     * Truyền vào mảng id item giỏ hàng để xóa.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id[] integer required
     * Danh sách id item giỏ hàng. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     */
    public function delete(DeleteShoppingCartRequest $request)
    {
        $response = $this->service->deleteMultiple($request);
        if ($response === true) {
            return response()->json([
                'status' => 200,
                'message' => __('success')
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => __('fail')
        ], 400);
    }

    /**
     * Đặt hàng
     *
     * Tiến hành đặt hàng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam id[] integer required
     * Danh sách id item giỏ hàng. Example: 1
     *
     * @bodyParam points integer
     * Số xu muốn sử dụng để giảm giá. Example: 50000
     *
     * @bodyParam discount_code string
     * Mã giảm giá. Example: SALE10
     *
     * @bodyParam voucher_shipping_id string
     * ID Voucher giảm giá vận chuyển. Example: 1
     *
     * @bodyParam voucher_product_id string
     * ID Voucher giảm giá tiền hàng. Example: 2
     *
     * @bodyParam order[payment_image] file
     * Hình ảnh chuyển khoản. Example: file.png
     *
     * @bodyParam order[payment_method] string required
     * Phương thức thanh toán. Example: "1"
     *
     * @bodyParam order[email] string required
     * Email của người đặt hàng. Example: "example@example.com"
     *
     * @bodyParam order[province_id] integer required
     * ID của tỉnh. Example: 1
     *
     * @bodyParam order[ward_id] integer required
     * ID của xã/phường. Example: 100
     *
     * @bodyParam order[fullname] string required
     * Họ tên đầy đủ của người nhận. Example: "Nguyen Van A"
     *
     * @bodyParam order[address] string required
     * Địa chỉ nhận hàng. Example: "123 Nguyen Trai, Ha Noi"
     *
     * @bodyParam order[phone] string required
     * Số điện thoại người nhận. Example: "0123456789"
     *
     * @bodyParam order[note] string
     * Ghi chú đơn hàng. Example: "Giao hàng giờ hành chính"
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Đặt hàng thành công.",
     *      "order": {
     *          "id": 411,
     *          "discount_code": "SALE10",
     *          "customer_fullname": "Nguyễn Văn A",
     *          "customer_phone": "0123456789",
     *          "customer_email": "example@example.com",
     *          "shipping_address": "123 Đường ABC",
     *          "note": "Ghi chú",
     *          "customer_name_other": "Nguyễn Thị B",
     *          "customer_phone_other": "0987654321",
     *          "shipping_address_other": "456 Đường XYZ",
     *          "note_other": "Ghi chú khác",
     *          "total": 60000000,
     *          "points_discount_value": null,
     *          "voucher_shipping_code": "VOUCHER3",
     *          "voucher_shipping_discount_value": 2500,
     *          "voucher_product_code": "VOUCHER4",
     *          "voucher_product_discount_value": 50000,
     *          "discount_value": 30000,
     *          "shipping_fee": 0,
     *          "code": "HDA14147",
     *          "qr_image": null,
     *          "status": "Chờ xác nhận",
     *          "payment_status": "Chưa thanh toán",
     *          "payment_method": "Chuyển khoản ngân hàng",
     *          "payment_image": null,
     *          "created_at": "2025-01-15T17:26:30.000000Z",
     *          "province": "Tỉnh Tuyên Quang",
     *          "ward": "Xã Tân Mỹ",
     *          "order_details": [
     *              {
     *                  "id": 1,
     *                  "name": "Laptop Dell Inspiron",
     *                  "qty": 5,
     *                  "unit_price": 12000000,
     *                  "slug": "laptop-dell-inspiron",
     *                  "avatar": "http://localhost:8080/CoreBanHang/userfiles/images/laptop/Dell-Inspiron-14-Plus-7430-laptop365-2.png"
     *              }
     *          ]
     *      }
     * }
     *
     * @response 500 {
     *      "status": 500,
     *      "message": "Đặt hàng thất bại."
     * }

     */
    public function checkout(CheckoutRequest $request)
    {
        $response = $this->service->checkout($request);
        if (!$response) {
            return response()->json([
                'status' => 400,
                'message' => __('order.create_failed'),
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => __('order.create_success'),
            'order' => new ShowOrderResource($response)
        ]);
    }

    private function calculatePointsDiscount($subtotal, $discountValue, $points, $exchangePercent, $maxDiscountPointsPercent)
    {
        if (!$points || $points <= 0) {
            return ['points_used' => 0, 'value' => 0];
        }

        // Tổng tiền sau khi trừ discount code
        $orderTotal = $subtotal - $discountValue;

        // Tính số points tối đa có thể dùng dựa trên exchange_percent
        $maxPointsByExchange = floor($orderTotal / $exchangePercent);

        // Lấy giá trị nhỏ nhất
        $maxPointsCanUse = min($points, $maxPointsByExchange);

        // Giá trị giảm giá từ points
        $pointsDiscountValue = $maxPointsCanUse * $exchangePercent;

        return [
            'points_used' => $maxPointsCanUse,
            'value' => $pointsDiscountValue
        ];
    }

    private function errorResponse($message, $subtotal = 0)
    {
        return response()->json([
            'status' => 400,
            'message' => $message,
            'data' => [
                'subtotal' => $subtotal,
                'shipping_fee' => 0,
                'discount_code' => null,
                'discount_code_discount_value' => 0,
                'voucher_product' => [
                    'id' => null,
                    'code' => null,
                    'value' => 0,
                    'min_order_amount' => null,
                ],
                'voucher_shipping' => [
                    'id' => null,
                    'code' => null,
                    'value' => 0,
                    'min_order_amount' => null,
                ],
                'points' => [
                    'points_used' => 0,
                    'value' => 0,
                ],
                'membership_discount_percentage' => 0,
                'membership_discount_value' => 0,
                'membership_shipping_discount_value' => 0,
                'total_discount' => 0,
                'final_total' => $subtotal,
            ]
        ], 400);
    }

    private function successResponse($subtotal, $discount, $voucherShipping, $voucherProduct, $shippingFee, $points = 0, $user = null)
    {
        $settings = $this->getSettings();

        // Tính toán các giá trị giảm giá
        $discountValue = $discount ? $this->service->calculateDiscountValue($subtotal, $discount) : 0;
        $voucherShippingValue = $voucherShipping ? $this->service->calculateShippingDiscountValue($subtotal, $voucherShipping, $shippingFee) : 0;
        $voucherProductValue = $voucherProduct ? $this->service->calculateDiscountValue($subtotal, $voucherProduct) : 0;

        // Tính toán points discount
        $exchangePercent = (float) $settings['exchange_percent'];
        $maxDiscountPointsPercent = (float) $settings['max_discount_points'];
        $pointsDiscount = $this->calculatePointsDiscount($subtotal, $discountValue, $points, $exchangePercent, $maxDiscountPointsPercent);

        // Tính membership discount
        $membershipDiscountPercentage = 0;
        $membershipDiscountValue = 0;
        $membershipShippingDiscountValue = 0;
        if ($user && $user->membership_id) {
            if (!$user->relationLoaded('member')) {
                $user->load('member');
            }
            if ($user->member) {
                $membershipDiscountPercentage = $user->member->discount_percentage ?? 0;
                $membershipDiscountValue = round($subtotal * ($membershipDiscountPercentage / 100));
                
                $shippingDiscountAmount = $user->member->shipping_discount_amount ?? 0;
                $membershipShippingDiscountValue = min($shippingDiscountAmount, $shippingFee);
            }
        }

        // Tổng giảm giá (không bao gồm points)
        $totalDiscount = $discountValue + $voucherProductValue + $voucherShippingValue + $membershipDiscountValue + $membershipShippingDiscountValue;

        // Tổng tiền cuối cùng (sau khi trừ tất cả discount và points, cộng shipping)
        $finalTotal = $subtotal - $totalDiscount - $pointsDiscount['value'] + $shippingFee;

        return response()->json([
            'status' => 200,
            'message' => __('shopping_cart.apply_discount_success'),
            'data' => [
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_code' => $discount ? $discount->code : null,
                'discount_code_discount_value' => $discountValue,
                'voucher_product' => [
                    'id' => $voucherProduct ? $voucherProduct->id : null,
                    'code' => $voucherProduct ? $voucherProduct->code : null,
                    'value' => $voucherProductValue,
                    'min_order_amount' => $voucherProduct ? $voucherProduct->min_order_amount : null,
                ],
                'voucher_shipping' => [
                    'id' => $voucherShipping ? $voucherShipping->id : null,
                    'code' => $voucherShipping ? $voucherShipping->code : null,
                    'value' => $voucherShippingValue,
                    'min_order_amount' => $voucherShipping ? $voucherShipping->min_order_amount : null,
                ],
                'points' => [
                    'points_used' => $pointsDiscount['points_used'],
                    'value' => $pointsDiscount['value'],
                ],
                'membership_discount_percentage' => $membershipDiscountPercentage,
                'membership_discount_value' => $membershipDiscountValue,
                'membership_shipping_discount_value' => $membershipShippingDiscountValue,
                'total_discount' => $totalDiscount,
                'final_total' => $finalTotal,
            ]
        ]);
    }

    private function getSettings()
    {
        $settings = $this->settingRepository->getAll();
        return [
            'exchange_percent' => $settings->where('setting_key', 'exchange_percent')->first()->plain_value ?? 0,
            'amount_to_exchange' => $settings->where('setting_key', 'amount_to_exchange')->first()->plain_value ?? 0,
            'free_shipping_target' => $settings->where('setting_key', 'free_shipping_target')->first()->plain_value ?? 0,
            'is_free_shipping_target_valid' => $settings->where('setting_key', 'is_free_shipping_target_valid')->first()->plain_value ?? 0,
            'min_order_to_exchange' => $settings->where('setting_key', 'min_order_to_exchange')->first()->plain_value ?? 0,
            'max_discount_points' => $settings->where('setting_key', 'max_discount_points')->first()->plain_value ?? 0,
        ];
    }

    private function getVoucher($voucherId)
    {
        return $voucherId ? $this->voucherRepository->find($voucherId) : null;
    }

    private function isDiscountInvalid($total, $discount, $voucherShipping, $voucherProduct)
    {
        // Check discount code validity
        if ($discount) {
            if ($total < $discount->min_order_amount || $discount->max_usage <= 0) {
                return true;
            }
            if ($discount->date_start > now() || $discount->date_end < now()) {
                return true;
            }
        }

        // Check voucher validity
        if ($voucherShipping) {
            if ($total < $voucherShipping->min_order_amount) {
                return true;
            }
            if (($voucherShipping->date_start && $voucherShipping->date_start > now()) ||
                ($voucherShipping->date_end && $voucherShipping->date_end < now()->startOfDay())) {
                return true;
            }
        }

        if ($voucherProduct) {
            if ($total < $voucherProduct->min_order_amount) {
                return true;
            }
            if (($voucherProduct->date_start && $voucherProduct->date_start > now()) ||
                ($voucherProduct->date_end && $voucherProduct->date_end < now()->startOfDay())) {
                return true;
            }
        }

        return false;
    }


    /**
     * Áp dụng mã giảm giá và tính phí vận chuyển
     *
     * Áp dụng mã giảm giá và tính phí vận chuyển trước khi đặt hàng.
     *
     * Trong đó các thông số cấu hình dc quy định như sau:<br>
     * <strong>free_shipping_target</strong>: mục tiêu của tổng đơn hàng để dc free ship<br>
     * <strong>is_free_shipping_target_valid</strong>: chương trình mục tiêu có dc bật hay không<br>
     * <strong>amount_to_exchange</strong>: số tiền đơn hàng để đổi dc 1 điểm<br>
     * <strong>exchange_percent</strong>: mỗi 1 điểm tương ứng bao nhiêu tiền<br>
     * <strong>max_discount_points</strong>: phần trăm tối đa có thể giảm giá bằng điểm cho tổng đơn hàng<br>
     * <strong>min_order_to_exchange</strong>: Tổng đơn hàng tối thiểu để có thể tích điểm<br>
     *
     * <strong>LƯU Ý: Đối với case tính giá vận chuyển phải truyền đủ id của tỉnh, quận, huyện mới có thể tính được.</strong>
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam id[] integer required
     * Danh sách id item giỏ hàng. Example: 1
     *
     * @bodyParam discount_code string
     * Mã giảm giá. Example: SALE10
     *
     * @bodyParam voucher_shipping_id string
     * ID Voucher giảm giá vận chuyển. Example: 1
     *
     * @bodyParam voucher_product_id string
     * ID Voucher giảm giá tiền hàng. Example: 2
     *
     * @bodyParam province_id integer
     * ID của tỉnh. Example: 1
     *
     * @bodyParam ward_id integer
     * ID của xã/phường. Example: 100
     *
     * @bodyParam points integer
     * Số điểm muốn sử dụng để giảm giá. Example: 1000
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Áp dụng mã giảm giá thành công.",
     *      "data": {
     *          "subtotal": 4500000,
     *          "shipping_fee": 95000,
     *          "discount_code": "SALE10",
     *          "discount_code_discount_value": 450000,
     *          "voucher_product": {
     *              "id": 2,
     *              "code": "VOUCHER4",
     *              "value": 50000,
     *              "min_order_amount": 2000000
     *          },
     *          "voucher_shipping": {
     *              "id": 1,
     *              "code": "VOUCHER3",
     *              "value": 25000,
     *              "min_order_amount": 1000000
     *          },
     *          "points": {
     *              "points_used": 1000,
     *              "value": 5000
     *          },
     *          "total_discount": 525000,
     *          "final_total": 4420000,
     *          "settings": {
     *              "exchange_percent": "5",
     *              "amount_to_exchange": "100",
     *              "free_shipping_target": "5000000",
     *              "is_free_shipping_target_valid": "1",
     *              "min_order_to_exchange": "500000",
     *              "max_discount_points": "30"
     *          },
     *          "user_points": 15000
     *      }
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Mã giảm giá đã hết hoặc Đơn hàng chưa đủ điều kiện sử dụng mã giảm giá này.",
     *      "data": {
     *          "subtotal": 4500000,
     *          "shipping_fee": 0,
     *          "discount_code": null,
     *          "discount_code_discount_value": 0,
     *          "voucher_product": {
     *              "id": null,
     *              "code": null,
     *              "value": 0,
     *              "min_order_amount": null
     *          },
     *          "voucher_shipping": {
     *              "id": null,
     *              "code": null,
     *              "value": 0,
     *              "min_order_amount": null
     *          },
     *          "points": {
     *              "points_used": 0,
     *              "value": 0
     *          },
     *          "total_discount": 0,
     *          "final_total": 4500000,
     *          "settings": {
     *              "exchange_percent": "5",
     *              "amount_to_exchange": "100",
     *              "free_shipping_target": "5000000",
     *              "is_free_shipping_target_valid": "1",
     *              "min_order_to_exchange": "500000",
     *              "max_discount_points": "30"
     *          },
     *          "user_points": 15000
     *      }
     * }

     */
    public function applyDiscountCode(ApplyDiscountCodeRequest $request)
    {
        $user = $this->getCurrentUser();

        // Lấy danh sách sản phẩm từ request
        $shoppingCart = $request->input('products');

        // Tính tổng tiền hàng (subtotal)
        $subtotal = $this->service->calculateTotal($shoppingCart);

        // Lấy các mã giảm giá và voucher
        $discountCode = $request->input('discount_code');
        $voucherShippingId = $request->input('voucher_shipping_id');
        $voucherProductId = $request->input('voucher_product_id');
        $points = $request->input('points', 0);

        $discount = $discountCode ? $this->discountRepository->findByField('code', $discountCode) : null;
        $voucherShipping = $this->getVoucher($voucherShippingId);
        $voucherProduct = $this->getVoucher($voucherProductId);

        // Tính phí vận chuyển
        $shippingFee = $this->calculateShippingFeeFromRequest($request, $subtotal);

        // Kiểm tra tính hợp lệ của mã giảm giá và voucher
        if ($user) {
            // User đã đăng nhập - có thể sử dụng voucher và points
            if ($this->isDiscountInvalid($subtotal, $discount, $voucherShipping, $voucherProduct)) {
                return $this->errorResponse(
                    __('discount.invalid'),
                    $subtotal
                );
            }

            // Kiểm tra points có đủ không
            if ($points > 0 && $points > $user->points) {
                return $this->errorResponse(
                    __('shopping_cart.points_exceeded', ['points' => $user->points]),
                    $subtotal
                );
            }
        } else {
            // User chưa đăng nhập - chỉ có thể sử dụng discount code
            if ($discount) {
                if ($subtotal < $discount->min_order_amount || $discount->max_usage <= 0) {
                    return $this->errorResponse(
                        __('discount.invalid_or_insufficient_order_amount', [
                            'current_amount' => format_price($subtotal),
                            'min_amount' => format_price($discount->min_order_amount)
                        ]),
                        $subtotal
                    );
                }
            }
            // Không cho phép sử dụng voucher và points khi chưa đăng nhập
            $voucherShipping = null;
            $voucherProduct = null;
            $points = 0;
        }

        return $this->successResponse($subtotal, $discount, $voucherShipping, $voucherProduct, $shippingFee, $points, $user);
    }

    private function calculateShippingFeeFromRequest($request, $total)
    {
        return $request->input('province_id') ? $this->calculateShippingFee(
            $request->input('province_id'),
            $request->input('ward_id'),
            $total
        ) : 0;
    }

    /**
     * Tạo url thanh toán vnpay sau khi đặt hàng
     *
     * Tiến hành Tạo url thanh toán vnpay sau khi đặt hàng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam order_id integer required
     * id đơn hàng cần thanh toán. Example: 1
     *
     * @bodyParam language string required
     * Mã ngôn ngữ. Example: "vn"
     *
     * @bodyParam bankcode string required
     * Mã ngân hàng. Example: "ncb"
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Khởi tạo thành công.",
     *      "redirect_url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=15000000&vnp_BankCode=ncb&vnp_Command=pay&vnp_CreateDate=20241107200136&vnp_CurrCode=VND&vnp_IpAddr=%3A%3A1&vnp_Locale=vn&vnp_OrderInfo=Thanh+to%C3%A1n+tr%E1%BA%A3+g%C3%B3p+%C4%91%E1%BB%A3t+3+cho+h%C3%B3a+%C4%91%C6%A1n+HD5EEEE1730975142&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A8080%2F2976-AppBanSach%2Fapi%2Fv1%2Fshopping-cart%2Freturn-installment-vnpay&vnp_TmnCode=91FYLEJN&vnp_TxnRef=98&vnp_Version=2.1.0&vnp_SecureHash=229468f03afe6b0f482d9ec738aa7a1cbf0e1ab13b9867cf11fed80633aab497161ce14596cc00360592c2802f0c267c4b877c1e4c5a49ce0802130c91b8a77f"
     * }
     *
     * @response 500 {
     *      "status": 500,
     *      "message": "Thao tác thất bại."
     * }

     */
    public function createPaymentVnpay(Request $request)
    {
        return $this->service->handleVnpay($request);
    }

    public function handleVnpayReturn(Request $request)
    {
        return $this->service->handleVnpayReturn($request);
    }

    /**
     * Đổi biến thể sản phẩm trong giỏ hàng
     *
     * Đổi biến thể sản phẩm trong giỏ hàng của user.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id integer required
     * id item giỏ hàng. Example: 1
     *
     * @bodyParam product_id integer required
     * id sản phẩm. Example: 20
     *
     * @bodyParam product_variation_id integer required
     * id biến thể sản phẩm mới. Example: 25
     *
     * @bodyParam qty integer required
     * Số lượng sản phẩm. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Đổi biến thể thành công."
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Item không có trong giỏ hàng."
     * }
     */
    public function changeVariation(ChangeVariationShoppingCartRequest $request)
    {
        $response = $this->service->changeVariation($request);
        if ($response === true || is_array($response)) {
            return response()->json([
                'status' => 200,
                'message' => __('shopping_cart.change_variation_success'),
            ], 200);
        }
        return response()->json([
            'status' => 400,
            'message' => __('shopping_cart.change_variation_failed')
        ], 400);
    }
}
