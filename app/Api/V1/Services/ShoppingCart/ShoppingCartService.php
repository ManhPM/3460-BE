<?php

namespace App\Api\V1\Services\ShoppingCart;

use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Setup;
use App\Api\V1\Repositories\Order\OrderRepositoryInterface;
use App\Api\V1\Services\ShoppingCart\ShoppingCartServiceInterface;
use App\Api\V1\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use App\Api\V1\Repositories\Product\{ProductRepositoryInterface, ProductVariationRepositoryInterface};
use App\Enums\Discount\DiscountValueType;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Product\ProductType;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use App\Traits\CalculateShippingFee;
use App\Traits\SendMail;
use App\Traits\SendNotification;
use App\Traits\UseLog;
use App\Exceptions\FlashSaleExceededException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\AdminInventory;
use App\Models\Admin;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

class ShoppingCartService implements ShoppingCartServiceInterface
{
    use UseLog, AuthService, Setup, SendMail, SendNotification, CalculateShippingFee;
    protected $data;
    protected $orderDetails;

    protected $repository;
    protected $orderRepository;
    protected $orderDetailRepository;
    protected $productRepository;
    protected $productVariationRepository;
    protected $discountRepository;
    protected $bankRepository;
    protected $userRepository;
    protected $settingRepository;
    protected $voucherRepository;
    protected $fileService;

    protected $transactionRepository;

    public function __construct(
        ShoppingCartRepositoryInterface $repository,
        OrderRepositoryInterface $orderRepository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariationRepositoryInterface $productVariationRepository,
        DiscountRepositoryInterface $discountRepository,
        TransactionRepositoryInterface $transactionRepository,
        BankRepositoryInterface $bankRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository,
        VoucherRepositoryInterface $voucherRepository,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->productRepository = $productRepository;
        $this->productVariationRepository = $productVariationRepository;
        $this->discountRepository = $discountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->bankRepository = $bankRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->voucherRepository = $voucherRepository;
        $this->fileService = $fileService;
    }

    public function handleVnpay(Request $request)
    {
        DB::beginTransaction();
        try {
            $language = $request->get('language');
            $orderId = $request->get('order_id');
            $bankcode = $request->get('bankcode');

            $order = $this->orderRepository->find($orderId);
            $transactionData = [
                'vnp_Amount' => ($order->total - $order->discount_value - $order->points - ($order->membership_discount_value ?? 0)) * 100,
                'vnp_BankCode' => $bankcode,
                'vnp_OrderInfo' => 'Thanh toan don hang #' . $order->code,
                'vnp_TmnCode' => env('VNP_TMNCODE'),
                'vnp_TxnRef' => $order->code,
                'expires_at' => now()->addMinutes(15),
            ];
            $items = $this->transactionRepository->getBy(['vnp_TxnRef' => $transactionData['vnp_TxnRef']]);
            foreach ($items as $item) {
                $item->delete();
            }

            $this->transactionRepository->create($transactionData);

            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('api.v1.handleVnpayReturn');
            $vnp_HashSecret = env('VNP_HASHSECRET'); //Chuỗi bí mật
            $vnp_OrderType = 'billpayment';
            $vnp_Locale = $language;
            $vnp_BankCode = $bankcode;
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => env('VNP_TMNCODE'),
                "vnp_Amount" => $transactionData['vnp_Amount'],
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => request()->ip(),
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $transactionData['vnp_OrderInfo'],
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $transactionData['vnp_TxnRef']
            );

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }
            if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                $inputData['vnp_Bill_State'] = $vnp_Bill_State;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => __('payment.vnpay_init_success'),
                'redirect_url' => $vnp_Url,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => __('payment.vnpay_init_failed'),
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function handleVnpayReturn(Request $request)
    {
        try {
            DB::beginTransaction();
            $inputData = $request->all();
            $transaction = $this->transactionRepository->findByField('vnp_TxnRef', $inputData['vnp_TxnRef']);
            $order = $this->orderRepository->findByField('code', $inputData['vnp_TxnRef']);
            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                if ($transaction && $transaction->vnp_Amount == $inputData['vnp_Amount'] && $transaction->expires_at > now() && $transaction->status == TransactionStatus::Pending->value) {
                    $transaction->update([
                        'status' => TransactionStatus::Success
                    ]);
                    $order->update([
                        'payment_status' => PaymentStatus::Paid
                    ]);
                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'message' => __('payment.success')
                    ], 200);
                }
            }
            $transaction->update([
                'status' => TransactionStatus::Failed
            ]);
            DB::commit();
            return response()->json([
                'status' => 400,
                'message' => __('payment.failed')
            ], 400);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Error handling VNPAY return.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function calculateDiscountValue($total, $discountOrVoucher)
    {
        $discountValue = 0;

        if ($total >= $discountOrVoucher->min_order_amount) {
            if ($discountOrVoucher->type == DiscountValueType::Percent) {
                $discountValue = $total * ($discountOrVoucher->discount_value / 100);
            } else {
                $discountValue = $discountOrVoucher->discount_value;
            }

            if (isset($discountOrVoucher->max_discount_value) && $discountValue > $discountOrVoucher->max_discount_value) {
                $discountValue = $discountOrVoucher->max_discount_value;
            }
        }

        return $discountValue;
    }

    public function calculateShippingDiscountValue($total, $discountOrVoucher, $shippingFee)
    {
        $discountValue = 0;

        if ($total >= $discountOrVoucher->min_order_amount) {
            if ($discountOrVoucher->type == DiscountValueType::Percent) {
                $discountValue = $shippingFee * ($discountOrVoucher->discount_value / 100);
            } else {
                $discountValue = $discountOrVoucher->discount_value;
            }

            if (isset($discountOrVoucher->max_discount_value) && $discountValue > $discountOrVoucher->max_discount_value) {
                $discountValue = $discountOrVoucher->max_discount_value;
            }
        }

        return $discountValue;
    }

    public function handleDiscountAndVoucher()
    {
        if (isset($this->data['discount_code'])) {
            $discount = $this->discountRepository->findByField('code', $this->data['discount_code']);
            $this->data['order']['discount_value'] = $this->calculateDiscountValue($this->data['order']['total'], $discount);
            $this->data['order']['discount_code'] = $this->data['discount_code'];
            $discount->max_usage = $discount->max_usage - 1;
            $discount->save();
        }
        if (isset($this->data['voucher_shipping_id'])) {
            $voucherShipping = $this->voucherRepository->find($this->data['voucher_shipping_id']);
            $this->data['order']['voucher_shipping_discount_value'] = $this->calculateShippingDiscountValue($this->data['order']['total'], $voucherShipping, 1200);
            $this->data['order']['voucher_shipping_code'] = $voucherShipping->code;
            $voucherShipping->is_used = 1;
            $voucherShipping->save();
        }
        if (isset($this->data['voucher_product_id'])) {
            $voucherProduct = $this->voucherRepository->find($this->data['voucher_product_id']);
            $this->data['order']['voucher_product_discount_value'] = $this->calculateDiscountValue($this->data['order']['total'], $voucherProduct);
            $this->data['order']['voucher_product_code'] = $voucherProduct->code;
            $voucherProduct->is_used = 1;
            $voucherProduct->save();
        }
    }

    private function handleMembershipDiscount($user)
    {
        if (!$user || !$user->membership_id) {
            $this->data['order']['membership_discount_percentage'] = 0;
            $this->data['order']['membership_discount_value'] = 0;
            $this->data['order']['membership_shipping_discount_value'] = 0;
            return;
        }

        // Load membership relationship if not loaded
        if (!$user->relationLoaded('member')) {
            $user->load('member');
        }

        if (!$user->member) {
            $this->data['order']['membership_discount_percentage'] = 0;
            $this->data['order']['membership_discount_value'] = 0;
            $this->data['order']['membership_shipping_discount_value'] = 0;
            return;
        }

        $discountPercentage = $user->member->discount_percentage ?? 0;
        $orderTotal = $this->data['order']['total'];

        // Tính giảm giá dựa trên phần trăm (làm tròn để đồng bộ với checkout)
        $discountValue = round($orderTotal * ($discountPercentage / 100));

        $this->data['order']['membership_discount_percentage'] = $discountPercentage;
        $this->data['order']['membership_discount_value'] = $discountValue;

        // Tính giảm phí vận chuyển từ hạng thành viên
        $shippingDiscountAmount = $user->member->shipping_discount_amount ?? 0;
        $shippingFee = $this->data['order']['shipping_fee'] ?? 0;
        $this->data['order']['membership_shipping_discount_value'] = min($shippingDiscountAmount, $shippingFee);
    }

    private function checkFlashSaleAvailability($shoppingCart)
    {
        foreach ($shoppingCart as $item) {
            $product = $this->productRepository->findOrFail($item->product_id);

            if ($product->is_flash_sale) {
                $flashSaleDetail = $product->is_flash_sale->details()->firstWhere('product_id', $product->id);
                $remainFlashSaleQty = $flashSaleDetail->qty - $flashSaleDetail->sold;

                if ($item->qty > $remainFlashSaleQty) {
                    throw new FlashSaleExceededException(
                        productName: $product->name,
                        remainingQty: $remainFlashSaleQty,
                        productId: $product->id,
                        isBuyNow: false,
                    );
                }
            }
        }
    }

    private function applyPoints($order, $user)
    {
        $orderTotal = $order->total - $order->discount_value - ($order->membership_discount_value ?? 0);
        $settings = $this->settingRepository->getAll();

        $exchangePercent = $settings->where('setting_key', 'exchange_percent')->first()->plain_value;

        // Xác định số xu thực tế có thể trừ
        $pointsToDeduct = min($this->data['points'], $orderTotal / $exchangePercent);

        $pointsDiscountValue = $pointsToDeduct * $exchangePercent;
        // Cập nhật xu của user và đơn hàng
        $this->userRepository->update($user->id, ['points' => $user->points - $pointsToDeduct]);
        $this->orderRepository->update($order->id, ['points' => $pointsToDeduct, 'points_discount_value' => $pointsDiscountValue]);
    }

    public function checkout(Request $request)
    {
        $this->data = $request->validated();
        $user = $this->getCurrentUser();
        $this->data['order']['status'] = OrderStatus::Pending;
        $this->data['order']['payment_status'] = PaymentStatus::Unpaid->value;
        $this->data['order']['code'] = $this->createCodeOrder();
        if ($this->data['order']['payment_method'] == PaymentMethod::Banking->value) {
            if (isset($this->data['order']['payment_image'])) {
                $this->data['order']['payment_image'] = $this->fileService->uploadAvatar('images', $this->data['order']['payment_image'], null);
                $this->data['order']['payment_status'] = PaymentStatus::Pending->value;
            }
        }
        DB::beginTransaction();
        try {
            if ($user) {
                $this->data['order']['user_id'] = $user->id;
                $shopping_cart = $this->repository->findManyById($this->data['id']);

                // Kiểm tra số lượng flash sale trước khi tiếp tục
                $this->checkFlashSaleAvailability($shopping_cart);

                // Kiểm tra tồn kho theo chi nhánh
                $adminId = data_get($this->data, 'order.admin_id');
                if (!$adminId) {
                    abort(400, 'Thiếu admin_id (chi nhánh).');
                }

                foreach ($shopping_cart as $item) {
                    $productId = $item->product_id;
                    $variationId = $item->product_variation_id ?? null;
                    $remainingQty = AdminInventory::where('admin_id', $adminId)
                        ->where('product_id', $productId)
                        ->when($variationId, fn($q) => $q->where('product_variation_id', $variationId))
                        ->when(!$variationId, fn($q) => $q->whereNull('product_variation_id'))
                        ->value('qty') ?? 0;
                    if ($item->qty > $remainingQty) {
                        $admin = Admin::find($adminId);
                        $branchName = $admin->branch_name ?? '';
                        abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '. Sản phẩm yêu cầu: ' . $item->qty . ', còn lại: ' . $remainingQty);
                    }
                }

                $this->data['order']['total'] = $this->calculateTotal($shopping_cart);
                $this->data['order']['shipping_fee'] = $this->calculateShippingFee(
                    $this->data['order']['province_id'],
                    $this->data['order']['ward_id'],
                    $this->data['order']['total']
                );
                $this->orderDetails = [];
                $this->prepareData($shopping_cart, $this->data['affiliate_code'] ?? null);
                $this->handleMembershipDiscount($user);
                $this->handleDiscountAndVoucher();
                $order = $this->orderRepository->create($this->data['order']);
                $this->storeOrderDetail($order->id, $this->orderDetails);

                // Send notification to admin
                $this->sendOrderNotificationToAdmin($order);

                // Wallet payment (API)

                if (($order->payment_method == PaymentMethod::Wallet) && $user) {
                    $order->refresh();
                    $payable = ($order->total + ($order->shipping_fee ?? 0))
                        - (($order->discount_value ?? 0) + ($order->voucher_shipping_discount_value ?? 0) + ($order->voucher_product_discount_value ?? 0) + ($order->membership_discount_value ?? 0) + ($order->membership_shipping_discount_value ?? 0) + ($order->points_discount_value ?? 0));
                    if ($user->wallet_balance >= $payable) {
                        $this->userRepository->update($user->id, ['wallet_balance' => $user->wallet_balance - $payable]);
                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'amount' => $payable,
                            'type' => WalletTransactionType::Payment->value,
                            'status' => WalletTransactionStatus::Approved->value,
                            'order_id' => $order->id,
                            'note' => 'Thanh toán đơn hàng bằng ví',
                        ]);
                        $this->orderRepository->update($order->id, ['payment_status' => PaymentStatus::Paid->value]);
                    } else {
                        DB::rollBack();
                        abort(400, 'Số dư ví không đủ để thanh toán.');
                    }
                }
                $shopping_cart->each(function ($item) {
                    $item->delete();
                });
                if (isset($this->data['points'])) {
                    $this->applyPoints($order, $user);
                }
                // $this->sendOrderNotification($order);
                DB::commit();
                return $order;
            } else {
                $cart = session('cart', []);
                $shopping_cart = collect($cart)->map(function ($item) {
                    return (object) $item;
                });
                $shopping_cart = $shopping_cart->whereIn('id', $this->data['id'])->values()->all();

                // Kiểm tra số lượng flash sale trước khi tiếp tục
                $this->checkFlashSaleAvailability($shopping_cart);

                // Kiểm tra tồn kho theo chi nhánh cho guest
                $adminId = data_get($this->data, 'order.admin_id');
                if (!$adminId) {
                    abort(400, 'Thiếu admin_id (chi nhánh).');
                }

                foreach ($shopping_cart as $item) {
                    $productId = $item->product_id;
                    $variationId = $item->product_variation_id ?? null;
                    $remainingQty = AdminInventory::where('admin_id', $adminId)
                        ->where('product_id', $productId)
                        ->when($variationId, fn($q) => $q->where('product_variation_id', $variationId))
                        ->when(!$variationId, fn($q) => $q->whereNull('product_variation_id'))
                        ->value('qty') ?? 0;
                    if ($item->qty > $remainingQty) {
                        $admin = Admin::find($adminId);
                        $branchName = $admin->branch_name ?? '';
                        abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '. Sản phẩm yêu cầu: ' . $item->qty . ', còn lại: ' . $remainingQty);
                    }
                }

                $this->data['order']['total'] = $this->calculateTotal($shopping_cart);
                $this->data['order']['shipping_fee'] = $this->calculateShippingFee(
                    $this->data['order']['province_id'],
                    $this->data['order']['ward_id'],
                    $this->data['order']['total']
                );
                $this->orderDetails = [];
                $this->prepareData($shopping_cart);
                $this->handleDiscountAndVoucher();
                $this->handleMembershipDiscount(null); // Guest user
                $order = $this->orderRepository->create($this->data['order']);
                $this->storeOrderDetail($order->id, $this->orderDetails);

                // Send notification to admin
                $this->sendOrderNotificationToAdmin($order);

                $idsToRemove = array_map('strval', (array) $this->data['id']);
                $updatedCart = array_values(array_filter($cart, function ($item) use ($idsToRemove) {
                    $itemId = is_array($item) ? ($item['id'] ?? null) : ($item->id ?? null);
                    return !in_array((string) $itemId, $idsToRemove, true);
                }));
                session(['cart' => $updatedCart]);
                DB::commit();
                return $order;
            }
        } catch (\Throwable $e) {
            $this->logError('Failed to process checkout: ', $e);
            DB::rollBack();
            throw $e;
        }
    }

    public function buyNow(Request $request)
    {
        $this->data = $request->validated();
        $user = $this->getCurrentUser();
        $this->data['order']['status'] = OrderStatus::Pending;
        $this->data['order']['payment_status'] = PaymentStatus::Unpaid->value;
        $this->data['order']['code'] = $this->createCodeOrder();
        if ($this->data['order']['payment_method'] == PaymentMethod::Banking->value) {
            if (isset($this->data['order']['payment_image'])) {
                $this->data['order']['payment_image'] = $this->fileService->uploadAvatar('images', $this->data['order']['payment_image'], null);
                $this->data['order']['payment_status'] = PaymentStatus::Pending->value;
            }
        }
        $this->data['order']['source'] = 'app';
        DB::beginTransaction();
        $product = $this->productRepository->findOrFail($this->data['product_id']);

        // Kiểm tra số lượng flash sale cho buy now
        if ($product->is_flash_sale) {
            $flashSaleDetail = $product->is_flash_sale->details()->firstWhere('product_id', $product->id);
            $remainFlashSaleQty = $flashSaleDetail->qty - $flashSaleDetail->sold;

            if ($this->data['qty'] > $remainFlashSaleQty) {
                throw new FlashSaleExceededException(
                    productName: $product->name,
                    remainingQty: $remainFlashSaleQty,
                    productId: $product->id,
                    isBuyNow: true,
                );
            }
        }

        $this->data['order']['total'] = $this->calculateTotalBuyNow($product, $this->data['qty'], $this->data['variation_id'] ?? null);
        $this->data['order']['shipping_fee'] = $this->calculateShippingFee(
            $this->data['order']['province_id'],
            $this->data['order']['ward_id'],
            $this->data['order']['total']
        );
        $this->orderDetails = [];
        $this->prepareDataBuyNow($product, $this->data['qty'], $this->data['variation_id'] ?? null, $this->data['affiliate_code'] ?? null);
        try {
            if ($user) {
                $this->data['order']['user_id'] = $user->id;
            }
            // Kiểm tra tồn kho theo chi nhánh
            $adminId = data_get($this->data, 'order.admin_id');
            if (!$adminId) {
                abort(400, 'Thiếu admin_id (chi nhánh).');
            }
            $variationId = $this->data['variation_id'] ?? null;
            $remainingQty = AdminInventory::where('admin_id', $adminId)
                ->where('product_id', $product->id)
                ->when($variationId, fn($q) => $q->where('product_variation_id', $variationId))
                ->when(!$variationId, fn($q) => $q->whereNull('product_variation_id'))
                ->value('qty') ?? 0;
            if ($remainingQty <= 0 || $this->data['qty'] > $remainingQty) {
                $admin = Admin::find($adminId);
                $branchName = $admin->branch_name ?? '';
                abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '.');
            }
            $this->handleDiscountAndVoucher();
            $this->handleMembershipDiscount($user);
            $order = $this->orderRepository->create($this->data['order']);
            $this->storeOrderDetail($order->id, $this->orderDetails);

            // Send notification to admin
            $this->sendOrderNotificationToAdmin($order);

            // Wallet payment (Buy Now - API)
            if (($order->payment_method == PaymentMethod::Wallet) && $user) {
                $order->refresh();
                $payable = ($order->total + ($order->shipping_fee ?? 0))
                    - (($order->discount_value ?? 0)
                        + ($order->voucher_shipping_discount_value ?? 0)
                        + ($order->voucher_product_discount_value ?? 0)
                        + ($order->membership_discount_value ?? 0)
                        + ($order->membership_shipping_discount_value ?? 0)
                        + ($order->points_discount_value ?? 0));

                if ($user->wallet_balance >= $payable) {
                    $this->userRepository->update($user->id, [
                        'wallet_balance' => $user->wallet_balance - $payable,
                    ]);

                    \App\Models\WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => -$payable,
                        'type' => WalletTransactionType::Payment->value,
                        'status' => WalletTransactionStatus::Approved->value,
                        'order_id' => $order->id,
                        'note' => 'Thanh toán đơn hàng (mua ngay) bằng ví',
                    ]);

                    $this->orderRepository->update($order->id, [
                        'payment_status' => \App\Enums\Order\PaymentStatus::Paid->value,
                    ]);
                } else {
                    DB::rollBack();
                    abort(400, 'Số dư ví không đủ để thanh toán.');
                }
            }

            if (isset($this->data['points']) && $user) {
                $this->applyPoints($order, $user);
            }
            DB::commit();
            return $order;
        } catch (\Throwable $e) {
            $this->logError('Failed to process buy now: ', $e);
            DB::rollBack();
            throw $e;
        }
    }


    private function calculateTotalBuyNow($product, $qty, $variation_id = null)
    {
        if (!$product->isSimple()) {
            $productVariation = $this->productVariationRepository->find($variation_id);
        }
        return !$product->isSimple()
            ? ($product->is_flash_sale ? $productVariation->flashsale_price * $qty : $productVariation->promotion_price * $qty)
            : ($product->is_flash_sale ? $product->flashsale_price * $qty : $product->promotion_price * $qty);
    }

    public function calculateTotal($shoppingCart)
    {
        $total = 0;

        if (is_array($shoppingCart) || $shoppingCart instanceof \Traversable) {
            foreach ($shoppingCart as $item) {
                $total += $this->calculateItemTotal($item);
            }
        } else {
            $total += $this->calculateItemTotal($shoppingCart);
        }
        return $total;
    }

    private function calculateItemTotal($item)
    {
        $item = (object) $item;
        if ($this->getCurrentUser()) {
            $product = $this->productRepository->findOrFail($item->product_id);
            if (!$product->isSimple() && isset($item->product_variation_id)) {
                $productVariation = $this->productVariationRepository->findOrFail($item->product_variation_id);
                return $product->is_flash_sale ? $productVariation->flashsale_price * $item->qty : $productVariation->promotion_price * $item->qty;
            }
            return $product->is_flash_sale ? $product->flashsale_price * $item->qty : $product->promotion_price * $item->qty;
        } else {
            $product = $this->productRepository->findOrFail($item->product_id);
            if (!$product->isSimple() && isset($item->variation_id)) {
                $productVariation = $this->productVariationRepository->findOrFail($item->variation_id);
                return $product->is_flash_sale ? $productVariation->flashsale_price * $item->qty : $productVariation->promotion_price * $item->qty;
            }
            return $product->is_flash_sale ? $product->flashsale_price * $item->qty : $product->promotion_price * $item->qty;
        }
    }

    protected function storeOrderDetail($orderId, $data)
    {
        if (empty($data) || !is_iterable($data)) {
            return;
        }
        foreach ($data as $item) {
            $item['order_id'] = $orderId;
            $this->orderDetailRepository->create($item);
        }
    }

    private function prepareData($cartItems, $affiliateCode = null)
    {
        $commissionRateSetting = $this->settingRepository->findByField('setting_key', 'commission_rate');
        $commissionRate = $commissionRateSetting ? $commissionRateSetting->plain_value : null;

        foreach ($cartItems as $item) {
            $item = (object) $item;
            $product = $this->productRepository->findOrFail($item->product_id);

            // Xác định giá sản phẩm
            if ($product->type == ProductType::Simple) {
                $unitPrice = $product->is_flash_sale ? $product->flashsale_price : $product->promotion_price;
            } else {
                if ($this->getCurrentUser()) {
                    $productVariation = $this->productVariationRepository->findOrFail($item->product_variation_id);
                } else {
                    $productVariation = $this->productVariationRepository->findOrFail($item->variation_id);
                }
                $unitPrice = $productVariation->product->is_flash_sale ? $productVariation->flashsale_price : $productVariation->promotion_price;
            }

            $affiliateEarning = 0;

            if ($commissionRate !== null && $affiliateCode !== null) {
                $affiliateEarning = $unitPrice * $item->qty * $commissionRate / 100;
            }

            // Thêm vào orderDetails
            $this->orderDetails[] = [
                'product_id' => $product->id,
                'unit_price' => $unitPrice,
                'product_variation_id' => isset($productVariation) ? $productVariation->id : null,
                'affiliate_code' => $affiliateCode,
                'affiliate_earning' => $affiliateEarning,
                'qty' => $item->qty,
                'product_name' => $product->name,
                'product_avatar' => $product->avatar ?? null,
                'product_slug' => $product->slug,
            ];
        }
    }

    private function prepareDataBuyNow($product, $qty, $variation_id = null, $affiliateCode = null)
    {
        $commissionRateSetting = $this->settingRepository->findByField('setting_key', 'commission_rate');
        $commissionRate = $commissionRateSetting ? $commissionRateSetting->plain_value : null;

        // Xác định giá sản phẩm
        if ($product->type == ProductType::Simple) {
            $unitPrice = $product->is_flash_sale ? $product->flashsale_price : $product->promotion_price;
        } else {
            $productVariation = $this->productVariationRepository->findOrFail($variation_id);
            $unitPrice = $productVariation->product->is_flash_sale ? $productVariation->flashsale_price : $productVariation->promotion_price;
        }

        $affiliateEarning = 0;

        if ($commissionRate !== null && $affiliateCode !== null) {
            $affiliateEarning = $unitPrice * $qty * $commissionRate / 100;
        }

        // Thêm vào orderDetails
        $this->orderDetails[] = [
            'product_id' => $product->id,
            'unit_price' => $unitPrice,
            'product_variation_id' => isset($productVariation) ? $productVariation->id : null,
            'affiliate_code' => $affiliateCode,
            'affiliate_earning' => $affiliateEarning,
            'qty' => $qty,
            'product_name' => $product->name,
            'product_avatar' => $product->avatar ?? null,
            'product_slug' => $product->slug,
        ];
    }


    public function store(Request $request)
    {
        $this->data = $request->validated();
        try {
            if ($this->getCurrentUser()) {
                DB::beginTransaction();
                $product = $this->productRepository->findOrFail($this->data['product_id']);

                // Lấy sản phẩm trong giỏ hàng nếu đã có
                $existingCartItem = $this->repository->getBy([
                    'user_id' => auth()->user()->id,
                    'product_id' => $this->data['product_id'],
                    'product_variation_id' => $product->isSimple() ? null : ($this->data['variation_id'] ?? null),
                ])->first();

                $currentQty = $existingCartItem ? $existingCartItem->qty : 0;
                $requestedQty = intval($this->data['qty']);
                $newQty = $currentQty + $requestedQty;

                // Kiểm tra tồn kho theo chi nhánh
                $adminId = $this->data['admin_id'] ?? null;
                if (!$adminId) {
                    abort(400, 'Thiếu admin_id (chi nhánh).');
                }
                $variationId = $product->isSimple() ? null : ($this->data['variation_id'] ?? null);
                $remainingQty = AdminInventory::where('admin_id', $adminId)
                    ->where('product_id', $this->data['product_id'])
                    ->when($variationId, fn($q) => $q->where('product_variation_id', $variationId))
                    ->when(!$variationId, fn($q) => $q->whereNull('product_variation_id'))
                    ->value('qty') ?? 0;
                if ($remainingQty <= 0) {
                    if ($existingCartItem) {
                        $existingCartItem->delete();
                    }
                    DB::commit();
                    $admin = Admin::find($adminId);
                    $branchName = $admin->branch_name ?? '';
                    abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '.');
                }
                if ($newQty > $remainingQty) {
                    $newQty = $remainingQty;
                }

                // Tiến hành thêm hoặc cập nhật số lượng
                $compare = [
                    'user_id' => auth()->user()->id,
                    'product_id' => $this->data['product_id'],
                    'admin_id' => $adminId,
                ];

                if ($product->type == ProductType::Variable) {
                    $compare['product_variation_id'] = $this->data['variation_id'];
                }

                $this->repository->updateOrCreate($compare, [
                    'qty' => $newQty,
                    'admin_id' => $adminId,
                ]);

                DB::commit();
                return true;
            } else {
                return $this->storeNotLogin($request);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function storeNotLogin(Request $request)
    {
        $this->data = $request->validated();

        // Lấy giỏ hàng từ session
        $cart = session('cart', []);

        // Tìm sản phẩm
        $product = $this->productRepository->findOrFail($this->data['product_id']);
        $productVariation = isset($this->data['variation_id']) ?
            $this->productVariationRepository->findOrFail($this->data['variation_id']) : null;

        // Kiểm tra tồn kho theo chi nhánh
        $adminId = $this->data['admin_id'] ?? null;
        if (!$adminId) {
            abort(400, 'Thiếu admin_id (chi nhánh).');
        }
        $variationId = $product->isSimple() ? null : ($this->data['variation_id'] ?? null);
        $remainingQty = AdminInventory::where('admin_id', $adminId)
            ->where('product_id', $this->data['product_id'])
            ->when($variationId, fn($q) => $q->where('product_variation_id', $variationId))
            ->when(!$variationId, fn($q) => $q->whereNull('product_variation_id'))
            ->value('qty') ?? 0;

        if ($remainingQty <= 0) {
            // Xóa sản phẩm khỏi giỏ hàng nếu đã có
            $cart = array_filter($cart, function ($item) use ($adminId) {
                return !($item['product_id'] == $this->data['product_id'] &&
                    $item['variation_id'] == ($this->data['variation_id'] ?? null) &&
                    ($item['admin_id'] ?? null) == $adminId);
            });

            session(['cart' => array_values($cart)]);
            $admin = Admin::find($adminId);
            $branchName = $admin->branch_name ?? '';
            abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '.');
        }

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $productExists = false;
        foreach ($cart as &$item) {
            if (
                $item['product_id'] == $this->data['product_id'] &&
                $item['variation_id'] == ($this->data['variation_id'] ?? null) &&
                ($item['admin_id'] ?? null) == $adminId
            ) {
                $availableQty = $remainingQty;
                $item['qty'] = min($item['qty'] + $this->data['qty'], $availableQty);
                $productExists = true;
                break;
            }
        }

        // Nếu sản phẩm chưa có trong giỏ hàng thì thêm mới
        if (!$productExists) {
            $availableQty = $remainingQty;
            $cart[] = [
                'id' => $this->uniqidReal(),
                'product_id' => $this->data['product_id'],
                'variation_id' => $this->data['variation_id'] ?? null,
                'admin_id' => $adminId,
                'qty' => min($this->data['qty'], $availableQty),
            ];
        }

        // Lưu giỏ hàng vào session
        session(['cart' => $cart]);

        return true;
    }


    public function update(Request $request)
    {
        $this->data = $request->validated();
        try {
            DB::beginTransaction();
            if ($this->getCurrentUser()) {
                $this->repository->updateMultiple($this->data['id'], $this->data['qty']);
                DB::commit();
                return true;
            } else {
                $cart = session('cart', []);
                foreach ($this->data['id'] as $key => $value) {
                    foreach ($cart as &$item) {
                        if ($item['id'] == $value) {
                            $item['qty'] = (int) $this->data['qty'][$key];
                            break;
                        }
                    }
                }
                DB::commit();
                return $cart;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to process update shopping cart: ', $e);
            throw $e;
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->getCurrentUser()) {
                $this->repository->deleteMultiple($request->input('id'));
                DB::commit();
                return true;
            } else {
                $cart = session('cart', []);
                $idsToDelete = $request->input('id', []);
                $cart = array_filter($cart, function ($item) use ($idsToDelete) {
                    return !in_array($item['id'], $idsToDelete);
                });
                session(['cart' => $cart]);
                DB::commit();
                return true;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to process delete shopping cart: ', $e);
            throw $e;
        }
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function changeVariation(Request $request)
    {
        $this->data = $request->validated();
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findOrFail($this->data['product_id']);
            $productVariation = $this->productVariationRepository->findOrFail($this->data['product_variation_id']);

            // Kiểm tra biến thể có thuộc về sản phẩm này không
            if ($productVariation->product_id != $product->id) {
                abort(400, 'Biến thể không thuộc về sản phẩm này.');
            }

            if ($this->getCurrentUser()) {
                // User đã đăng nhập
                $userId = auth()->user()->id;

                // Tìm item trong giỏ hàng theo id
                $cartItem = $this->repository->find($this->data['id']);

                if (!$cartItem || $cartItem->user_id != $userId) {
                    abort(400, 'Item không có trong giỏ hàng.');
                }

                if ($cartItem->product_id != $this->data['product_id']) {
                    abort(400, 'Product ID không khớp với item trong giỏ hàng.');
                }

                // Kiểm tra xem có item nào khác trong giỏ hàng có cùng product_id và product_variation_id mới không
                $existingItemWithNewVariation = $this->repository->getBy([
                    'user_id' => $userId,
                    'product_id' => $this->data['product_id'],
                    'product_variation_id' => $this->data['product_variation_id'],
                    'admin_id' => $cartItem->admin_id,
                ])->first();

                // Lấy admin_id từ item hiện tại
                $adminId = $cartItem->admin_id;
                if (!$adminId) {
                    abort(400, 'Thiếu admin_id (chi nhánh).');
                }

                // Kiểm tra tồn kho của biến thể mới
                $remainingQty = AdminInventory::where('admin_id', $adminId)
                    ->where('product_id', $this->data['product_id'])
                    ->where('product_variation_id', $this->data['product_variation_id'])
                    ->value('qty') ?? 0;

                if ($remainingQty <= 0) {
                    DB::rollBack();
                    $admin = Admin::find($adminId);
                    $branchName = $admin->branch_name ?? '';
                    abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '.');
                }

                $requestedQty = intval($this->data['qty']);
                if ($requestedQty > $remainingQty) {
                    $requestedQty = $remainingQty;
                }

                if ($existingItemWithNewVariation && $existingItemWithNewVariation->id != $cartItem->id) {
                    // Nếu đã có item với biến thể mới, gộp lại
                    $newQty = $existingItemWithNewVariation->qty + $requestedQty;
                    if ($newQty > $remainingQty) {
                        $newQty = $remainingQty;
                    }
                    $existingItemWithNewVariation->update(['qty' => $newQty]);
                    // Xóa item cũ
                    $cartItem->delete();
                } else {
                    // Cập nhật item hiện tại với biến thể mới
                    $cartItem->update([
                        'product_variation_id' => $this->data['product_variation_id'],
                        'qty' => $requestedQty,
                    ]);
                }

                DB::commit();
                return true;
            } else {
                // User chưa đăng nhập - xử lý với session
                $cart = session('cart', []);

                // Tìm item trong giỏ hàng theo id
                $cartItemIndex = null;
                foreach ($cart as $index => $item) {
                    if (isset($item['id']) && $item['id'] == $this->data['id']) {
                        $cartItemIndex = $index;
                        break;
                    }
                }

                if ($cartItemIndex === null) {
                    abort(400, 'Item không có trong giỏ hàng.');
                }

                $cartItem = $cart[$cartItemIndex];

                if ($cartItem['product_id'] != $this->data['product_id']) {
                    abort(400, 'Product ID không khớp với item trong giỏ hàng.');
                }
                $adminId = $cartItem['admin_id'] ?? null;

                if (!$adminId) {
                    abort(400, 'Thiếu admin_id (chi nhánh).');
                }

                // Kiểm tra tồn kho của biến thể mới
                $remainingQty = AdminInventory::where('admin_id', $adminId)
                    ->where('product_id', $this->data['product_id'])
                    ->where('product_variation_id', $this->data['product_variation_id'])
                    ->value('qty') ?? 0;

                if ($remainingQty <= 0) {
                    DB::rollBack();
                    $admin = Admin::find($adminId);
                    $branchName = $admin->branch_name ?? '';
                    abort(400, 'Không đủ tồn kho tại chi nhánh ' . $branchName . '.');
                }

                $requestedQty = intval($this->data['qty']);
                if ($requestedQty > $remainingQty) {
                    $requestedQty = $remainingQty;
                }

                // Kiểm tra xem có item nào khác trong giỏ hàng có cùng product_id và variation_id mới không
                $existingItemIndex = null;
                foreach ($cart as $index => $item) {
                    if (
                        $index != $cartItemIndex &&
                        $item['product_id'] == $this->data['product_id'] &&
                        ($item['variation_id'] ?? null) == $this->data['product_variation_id'] &&
                        ($item['admin_id'] ?? null) == $adminId
                    ) {
                        $existingItemIndex = $index;
                        break;
                    }
                }

                if ($existingItemIndex !== null) {
                    // Nếu đã có item với biến thể mới, gộp lại
                    $newQty = $cart[$existingItemIndex]['qty'] + $requestedQty;
                    if ($newQty > $remainingQty) {
                        $newQty = $remainingQty;
                    }
                    $cart[$existingItemIndex]['qty'] = $newQty;
                    // Xóa item cũ
                    unset($cart[$cartItemIndex]);
                } else {
                    // Cập nhật item hiện tại với biến thể mới
                    $cart[$cartItemIndex]['variation_id'] = $this->data['product_variation_id'];
                    $cart[$cartItemIndex]['qty'] = $requestedQty;
                }

                // Lưu lại giỏ hàng
                session(['cart' => array_values($cart)]);
                DB::commit();
                return $cart;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to change variation in shopping cart: ', $e);
            throw $e;
        }
    }

    /**
     * Send notification to admin when a new order is created
     */
    private function sendOrderNotificationToAdmin($order)
    {
        try {
            // Get admin_id from order
            $adminId = $order->admin_id;
            if (!$adminId) {
                return;
            }

            // Prepare notification message
            $orderCode = $order->code ?? 'N/A';
            $customerName = $order->customer_fullname ?? 'Khách hàng';
            $totalAmount = number_format($order->total ?? 0, 0, ',', '.') . ' ' . config('custom.currency');

            $title = 'Đơn hàng mới';
            $message = "Bạn có đơn hàng mới: {$orderCode} từ {$customerName} với tổng tiền {$totalAmount}";

            // Send notification to admin
            $this->sendNotification($adminId, $title, $message, null, 'admin_id');
        } catch (\Exception $e) {
            // Log error but don't throw to avoid breaking order creation
            $this->logError('Failed to send order notification to admin: ', $e);
        }
    }

    /**
     * Lấy identifier để tạo cache key cho affiliate
     * Nếu user đã login: dùng user_id
     * Nếu chưa login: dùng hash của IP + User-Agent
     */
    private function getAffiliateIdentifier(): string
    {
        $user = $this->getCurrentUser();
        if ($user) {
            return 'user_' . $user->id;
        }

        // Tạo identifier từ IP và User-Agent cho guest
        $request = request();
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        return 'guest_' . md5($ip . $userAgent);
    }
}
