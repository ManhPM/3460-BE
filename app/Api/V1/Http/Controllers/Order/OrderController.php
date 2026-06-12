<?php

namespace App\Api\V1\Http\Controllers\Order;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Requests\Order\CancelOrderRequest;
use App\Api\V1\Http\Requests\Order\UpdateOrderRequest;
use App\Api\V1\Services\Order\OrderServiceInterface;
use App\Api\V1\Repositories\Order\OrderRepositoryInterface;
use App\Api\V1\Http\Requests\Order\OrderRequest;
use App\Api\V1\Http\Resources\Order\AllOrderDetailResource;
use App\Api\V1\Http\Resources\Order\AllOrderResource;
use App\Api\V1\Http\Resources\Order\ShowOrderResource;
use App\Api\V1\Repositories\Order\OrderDetailRepositoryInterface;
use App\Enums\Order\PaymentStatus;

/**
 * @group Đơn hàng
 */

class OrderController extends Controller
{
    use AuthService;
    protected $orderDetailRepository;
    private $fileService;
    public function __construct(
        OrderRepositoryInterface $repository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        OrderServiceInterface $service,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->service = $service;
        $this->fileService = $fileService;
    }
    /**
     * Danh sách đơn hàng
     *
     * Lấy danh sách đơn hàng của user.
     *
     * <strong>Trạng thái của đơn hàng bao gồm:</strong>
     * + 1: Chưa xác nhận
     * + 2: Đã xác nhận
     * + 3: Đã huỷ
     *
     * @queryParam status integer
     * Trạng thái của đơn hàng. Example: 1
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thành công.",
     *      "data": [
     *          {
     *              "id": 411,
     *              "discount_code": "SALE10",
     *              "customer_fullname": "Nguyễn Văn A",
     *              "customer_phone": "0123456789",
     *              "customer_email": "example@example.com",
     *              "shipping_address": "123 Đường ABC",
     *              "note": "Ghi chú",
     *              "customer_name_other": "Nguyễn Thị B",
     *              "customer_phone_other": "0987654321",
     *              "shipping_address_other": "456 Đường XYZ",
     *              "note_other": "Ghi chú khác",
     *              "total": 60000000,
     *              "points_discount_value": null,
     *              "voucher_shipping_code": "VOUCHER3",
     *              "voucher_shipping_discount_value": 2500,
     *              "voucher_product_code": "VOUCHER4",
     *              "voucher_product_discount_value": 50000,
     *              "discount_value": 30000,
     *              "shipping_fee": 0,
     *              "code": "HDA14147",
     *              "qr_image": null,
     *              "status": "Chờ xác nhận",
     *              "payment_status": "Chưa thanh toán",
     *              "payment_method": "Chuyển khoản ngân hàng",
     *              "payment_image": null,
     *              "created_at": "2025-01-15T17:26:30.000000Z",
     *              "province": "Tỉnh Tuyên Quang",
     *              "ward": "Xã Tân Mỹ",
     *              "order_details": [
     *                  {
     *                      "id": 1,
     *                      "name": "Laptop Dell Inspiron",
     *                      "qty": 5,
     *                      "unit_price": 12000000,
     *                      "slug": "laptop-dell-inspiron",
     *                      "avatar": "http://localhost:8080/CoreBanHang/userfiles/images/laptop/Dell-Inspiron-14-Plus-7430-laptop365-2.png"
     *                  }
     *              ]
     *          }
     *      ]
     * }

     */
    public function index(OrderRequest $request)
    {
        $filter = $request->validated();

        $orders = $this->repository->getByKeyAuthCurrent($filter);
        $orders = new AllOrderResource($orders);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $orders
        ]);
    }

    /**
     * DS Affiliate thành công
     *
     * Lấy DS Affiliate thành công của user.
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
     *         {
     *              "order_code": "HD7C3041733921941",
     *              "product_name": "Tủ lạnh Samsung",
     *              "total": "1500000",
     *              "affiliate_earnings": 50000
     *          }
     *      ]
     * }

     */
    public function affiliate()
    {
        $user = $this->getCurrentUser();
        $items = $this->orderDetailRepository->getAffiliate($user->affiliate_code);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AllOrderDetailResource($items)
        ]);
    }
    /**
     * Chi tiết đơn hàng
     *
     * Lấy chi tiết đơn hàng của user.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @pathParam id integer required
     * id của đơn hàng. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thành công.",
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

     */
    public function show($id)
    {
        $order = $this->repository->findOrFail($id);
        $order = new ShowOrderResource($order);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $order
        ]);
    }

    /**
     * Hủy đơn hàng
     *
     * Hủy đơn hàng của user.
     *
     * Trong đó các lý do (reason) huỷ đơn được cấu hình dc quy định như sau:<br>
     * <strong>2</strong>: Thay đổi ý định về sản phẩm<br>
     * <strong>3</strong>: Thay đổi địa chỉ<br>
     * <strong>4</strong>: Tìm thấy nơi khác giá tốt hơn<br>
     * <strong>5</strong>: Thủ tục thanh toán rắc rối
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id integer required
     * id đơn hàng. Example: 1
     *
     * @bodyParam reason integer required
     * Lý do huỷ đơn hàng. Example: 2
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }

     */
    public function cancel(CancelOrderRequest $request)
    {
        $response = $this->service->cancel($request);
        if ($response) {
            return response()->json([
                'status' => 200,
                'message' => __('success')
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => __('fail'),
        ], 400);
    }


    /**
     * Tải ảnh chuyển khoản
     *
     * Tải ảnh chuyển khoản cho đơn hàng của user.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam id integer required
     * id đơn hàng. Example: 1
     *
     * @bodyParam payment_image file required
     * File ảnh chuyển khoản. Example: file.png
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }

     */
    public function uploadPaymentImage(OrderRequest $request)
    {
        $paymentImage = $request->input('payment_image');
        $id = $request->input('id');
        if (isset($paymentImage)) {
            $order = $this->repository->find($id);
            if ($order) {
                $paymentImage = $this->fileService->uploadSingleFileBase64($paymentImage);
                $order->update(['payment_status' => PaymentStatus::Pending->value, 'payment_image' => $paymentImage]);
                return response()->json([
                    'status' => 200,
                    'message' => __('success')
                ]);
            }
            return response()->json([
                'status' => 500,
                'message' => __('fail'),
            ], 500);
        }
    }

    /**
     * Lấy mã QR chuyển khoản và thông tin chuyển khoản
     *
     * Lấy mã QR chuyển khoản cùng các thông tin chuyển khoản cho đơn hàng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @pathParam id integer required
     * id của đơn hàng. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "qr_image_url": "https://img.vietqr.io/image/VCB-1234567890-print.png?amount=1000000&addInfo=THANH%20TOAN%20DON%20HANG%20HD123&accountName=Nguyen%20Van%20A",
     *          "bank_name": "Vietcombank",
     *          "bank_branch": "Chi nhánh HCM",
     *          "account_number": "1234567890",
     *          "account_name": "Nguyen Van A",
     *          "total_amount": 1000000,
     *          "order_code": "HD123"
     *      }
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Đơn hàng này không sử dụng phương thức chuyển khoản ngân hàng."
     * }
     *
     */
    public function getBankTransferInfo($id)
    {
        try {
            $data = $this->service->getBankTransferInfo($id);
            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cập nhật đơn hàng
     *
     * Cập nhật thông tin ngân hàng và ảnh chuyển khoản cho đơn hàng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @pathParam id integer required
     * id của đơn hàng. Example: 1
     *
     * @bodyParam bank_id integer nullable
     * ID của ngân hàng. Example: 64
     *
     * @bodyParam payment_image string nullable
     * Ảnh chuyển khoản dạng base64 với data URI prefix. Example: data:image/jpeg;base64,/9j/4AAQSkZJRg...
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "id": 1,
     *          "bank_id": 64,
     *          "payment_image": "uploads/payment/xxx.jpg"
     *      }
     * }
     *
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        try {
            $data = $this->service->updateOrder($id, $request->validated());
            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
