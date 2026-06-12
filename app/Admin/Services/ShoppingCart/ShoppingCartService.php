<?php

namespace App\Admin\Services\ShoppingCart;

use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Services\ShoppingCart\ShoppingCartServiceInterface;
use App\Admin\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use App\Admin\Repositories\Product\{ProductRepositoryInterface, ProductVariationRepositoryInterface};
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Setup;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\PaymentStatus;
use App\Enums\Product\ProductType;
use App\Enums\Transaction\TransactionStatus;
use App\Traits\CalculateShippingFee;
use App\Traits\SendMail;
use App\Traits\UseLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingCartService implements ShoppingCartServiceInterface
{
    use UseLog, Setup, AuthService, SendMail, CalculateShippingFee;

    protected $fileService;

    protected $data;
    protected $orderDetails;

    protected $repository;
    protected $orderRepository;
    protected $productRepository;
    protected $productVariationRepository;
    protected $orderDetailRepository;
    protected $discountRepository;
    protected $transactionRepository;
    protected $userRepository;
    protected $settingRepository;
    protected $voucherRepository;

    public function __construct(
        ShoppingCartRepositoryInterface $repository,
        ProductRepositoryInterface $productRepository,
        ProductVariationRepositoryInterface $productVariationRepository,
        OrderRepositoryInterface $orderRepository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        DiscountRepositoryInterface $discountRepository,
        TransactionRepositoryInterface $transactionRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository,
        VoucherRepositoryInterface $voucherRepository,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->productVariationRepository = $productVariationRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->discountRepository = $discountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->voucherRepository = $voucherRepository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        DB::beginTransaction();
        try {
            $shoppingCart = $this->repository->getBy([
                'user_id' => $this->getCurrentUserId(),
                'product_id' => $this->data['product_id'],
                'product_variation_id' => $this->data['product_variation_id'] ?? null,
            ])->first();
            $product = $this->productRepository->find($this->data['product_id']);
            if (!$shoppingCart) {
                if ($product->isSimple()) {
                    if ($product->qty < $this->data['qty']) {
                        DB::rollBack();
                        return 1;
                    }
                } else {
                    $productVariation = $product->productVariations()->where('id', $this->data['product_variation_id'])->first();
                    if ($productVariation->qty < $this->data['qty']) {
                        DB::rollBack();
                        return 1;
                    }
                }
                $shoppingCart = $this->repository->create([
                    'user_id' => $this->getCurrentUserId(),
                    'product_id' => $this->data['product_id'],
                    'product_variation_id' => $this->data['product_variation_id'] ?? null,
                    'qty' => $this->data['qty'],
                ]);
            } else {
                if ($product->isSimple()) {
                    if ($product->qty < ($shoppingCart->qty + $this->data['qty'])) {
                        DB::rollBack();
                        return 1;
                    }
                } else {
                    $productVariation = $product->productVariations()->where('id', $this->data['product_variation_id'])->first();
                    if ($productVariation->qty < ($shoppingCart->qty + $this->data['qty'])) {
                        DB::rollBack();
                        return 1;
                    }
                }
                $shoppingCart->update(['qty' => $shoppingCart->qty + $this->data['qty']]);
            }
            DB::commit();
            return $shoppingCart;
        } catch (Exception $e) {
            $this->logError('Failed to process shopping cart: ', $e);
            DB::rollBack();
            return false;
        }
    }

    public function storeNotLogin(Request $request)
    {
        $this->data = $request->validated();
        $cart = session()->get('cart', []);
        $currentCartItem = null;
        $product = $this->productRepository->find($this->data['product_id']);
        foreach ($cart as $item) {
            if ($item['product_id'] == $this->data['product_id']) {
                if ($product->isSimple()) {
                    if ($product->qty < intval($this->data['qty']) + $item['qty']) {
                        return 1;
                    }
                } else {
                    $productVariation = $product->productVariations()->where('id', $this->data['product_variation_id'])->first();
                    if ($productVariation->qty < intval($this->data['qty']) + $item['qty']) {
                        return 1;
                    }
                }
            }
        }
        $productExists = false;
        foreach ($cart as &$item) {
            if (
                $item['product_id'] == $this->data['product_id'] &&
                $item['product_variation_id'] == ($this->data['product_variation_id'] ?? null)
            ) {
                $currentCartItem = $item;
                $item['qty'] += $this->data['qty'];
                $productExists = true;
                break;
            }
        }
        if (!$productExists) {
            $cart[] = [
                'id' => $this->uniqidReal(),
                'product' => $product,
                'productVariation' => isset($this->data['product_variation_id']) ? $product->productVariations()->where('id', $this->data['product_variation_id'])->first() : null,
                'product_id' => $this->data['product_id'],
                'product_variation_id' => $this->data['product_variation_id'] ?? null,
                'qty' => $this->data['qty'],
            ];
        }
        foreach ($cart as &$item) {
            if (
                $item['product_id'] == $this->data['product_id'] &&
                $item['product_variation_id'] == ($this->data['product_variation_id'] ?? null)
            ) {
                $currentCartItem = $item;
                break;
            }
        }
        session()->put('cart', $cart);
        session()->save();
        return (object) $currentCartItem;
    }

    public function update(Request $request)
    {
        $this->data = $request->validated();
        if ($this->getCurrentUser()) {
            DB::beginTransaction();
            try {
                $shoppingCart = $this->repository->findOrFail($this->data['id']);
                $shoppingCart->update(['qty' => $this->data['qty']]);
                DB::commit();
                return $shoppingCart;
            } catch (Exception $e) {
                $this->logError('Failed to update shopping cart: ', $e);
                DB::rollBack();
                return false;
            }
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as &$item) {
                if ($item['id'] == $this->data['id']) {
                    if (isset($item['productVariation'])) {
                        if ($item['productVariation']->qty < $this->data['qty']) {
                            return 1;
                        }
                    } else {
                        if ($item['product']->qty < $this->data['qty']) {
                            return 1;
                        }
                    }
                    $item['qty'] = $this->data['qty'];
                }
            }
            session()->put('cart', $cart);
            session()->save();
            return true;
        }
    }

    public function increament(Request $request)
    {
        $this->data = $request->validated();
        DB::beginTransaction();
        try {
            if ($this->getCurrentUser()) {
                $shoppingCart = $this->repository->findOrFail($this->data['id']);
                if ($shoppingCart->product_variation_id) {
                    $productVariation = $this->productVariationRepository->findOrFail($shoppingCart->product_variation_id);
                    if ($productVariation->qty < $shoppingCart->qty + 1) {
                        return 1;
                    }
                } else {
                    $product = $this->productRepository->findOrFail($shoppingCart->product_id);
                    if ($product->qty < $shoppingCart->qty + 1) {
                        return 1;
                    }
                }
                $shoppingCart->update(['qty' => $shoppingCart->qty + 1]);
                DB::commit();
                return true;
            } else {
                $cart = session()->get('cart', []);
                foreach ($cart as &$item) {
                    if ($item['id'] == $this->data['id']) {
                        if (isset($item['product_variation_id'])) {
                            $productVariation = $this->productVariationRepository->findOrFail($item['product_variation_id']);
                            if ($productVariation->qty < $item['qty'] + 1) {
                                return 1;
                            }
                        } else {
                            $product = $this->productRepository->findOrFail($item['product_id']);
                            if ($product->qty < $item['qty'] + 1) {
                                return 1;
                            }
                        }
                        $item['qty'] += 1;
                        break;
                    }
                }
                session()->put('cart', $cart);
                session()->save();
                DB::commit();
                return true;
            }
        } catch (Exception $e) {
            $this->logError('Failed to increment quantity in shopping cart: ', $e);
            DB::rollBack();
            return false;
        }
    }


    public function decreament(Request $request)
    {
        $this->data = $request->validated();
        if ($this->getCurrentUser()) {
            DB::beginTransaction();
            try {
                $shoppingCart = $this->repository->findOrFail($this->data['id']);
                if ($shoppingCart->qty > 1) {
                    $shoppingCart->update(['qty' => $shoppingCart->qty - 1]);
                } else {
                    $this->delete($shoppingCart->id);
                }
                DB::commit();
                return $shoppingCart;
            } catch (Exception $e) {
                $this->logError('Failed to decreament quantity shopping cart: ', $e);
                DB::rollBack();
                return false;
            }
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $key => &$item) {
                if ($item['id'] == $this->data['id']) {
                    if ($item['qty'] > 1) {
                        $item['qty'] -= 1;
                    } else {
                        unset($cart[$key]);
                    }
                }
            }
            session()->put('cart', array_values($cart));
            session()->save();
            return true;
        }
    }

    public function delete($id)
    {
        if ($this->getCurrentUser()) {
            return $this->repository->delete($id);
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $key => $item) {
                if ($item['id'] == $id) {
                    unset($cart[$key]);
                    break;
                }
            }
            session()->put('cart', array_values($cart));
            session()->save();
            return true;
        }
    }

    public function handleDiscountAndVoucher()
    {
        if (isset($this->data['code'])) {
            $discount = $this->discountRepository->findByField('code', $this->data['code']);
            $this->data['order']['discount_value'] = $this->calculateDiscountValue($this->data['order']['total'], $discount);
            $this->data['order']['discount_code'] = $this->data['code'];
            $discount->max_usage = $discount->max_usage - 1;
            $discount->save();
        }
        if (isset($this->data['voucher_shipping_id'])) {
            $voucherShipping = $this->voucherRepository->find($this->data['voucher_shipping_id']);
            $this->data['order']['voucher_shipping_discount_value'] = $this->calculateShippingDiscountValue($this->data['order']['total'], $voucherShipping, 50000);
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

    private function checkFlashSaleAvailability($cartItems)
    {
        foreach ($cartItems as $item) {
            $product = $item->product;

            if ($product->is_flash_sale) {
                $flashSaleDetail = $product->is_flash_sale->details()->firstWhere('product_id', $product->id);

                if ($flashSaleDetail) {
                    $remainFlashSaleQty = $flashSaleDetail->qty - $flashSaleDetail->sold;

                    if ($remainFlashSaleQty < $item->qty) {
                        abort(400, 'Số lượng sản phẩm flash sale không đủ. Sản phẩm: ' . $product->name . '. Còn lại: ' . $remainFlashSaleQty);
                    }
                }
            }
        }
    }

    private function applyPoints($order, $user)
    {
        $orderTotal = $order->total - $order->discount_value;
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
        $isBuyNow = $this->data['isBuyNow'];
        $this->data['order']['status'] = OrderStatus::Pending;
        $this->data['order']['code'] = $this->createCodeOrder();
        DB::beginTransaction();
        try {
            if ($user) {
                $this->data['order']['user_id'] = $user->id;
                $shopping_cart = $this->repository->findManyById($this->data['shopping_cart_id']);
                foreach ($shopping_cart as $item) {
                    if ($item['qty'] >= $this->data['qty'][$item->id]) {
                        $item['qty'] = $this->data['qty'][$item->id];
                    }
                }

                // Kiểm tra số lượng flash sale trước khi tiếp tục
                $this->checkFlashSaleAvailability($shopping_cart);

                $this->data['order']['total'] = $this->calculateTotal($shopping_cart);
                $this->data['order']['shipping_fee'] = $this->calculateShippingFee(
                    $this->data['order']['province_id'],
                    $this->data['order']['ward_id'],
                    $this->data['order']['total']
                );
                $this->prepareData($shopping_cart);
                $this->handleDiscountAndVoucher();
                $order = $this->orderRepository->create($this->data['order']);
                $this->storeOrderDetail($order->id, $this->orderDetails);

                $shopping_cart->each(function ($item) use ($isBuyNow) {
                    if (!$isBuyNow) {
                        $item->delete();
                    } else {
                        $instance = $this->repository->find($item->id);
                        $instance->update(['qty' => $instance->qty - $item->qty]);
                    }
                });

                // Handle wallet payment
                if ($order->payment_method == \App\Enums\Payment\PaymentMethod::Wallet) {
                    $order->refresh();
                    $payable = ($order->total + ($order->shipping_fee ?? 0))
                        - (($order->discount_value ?? 0) + ($order->voucher_shipping_discount_value ?? 0) + ($order->voucher_product_discount_value ?? 0));

                    if ($user->wallet_balance >= $payable) {
                        $this->userRepository->update($user->id, ['wallet_balance' => $user->wallet_balance - $payable]);
                        \App\Models\WalletTransaction::create([
                            'user_id' => $user->id,
                            'amount' => -$payable,
                            'type' => 'payment',
                            'status' => 'approved',
                            'order_id' => $order->id,
                            'note' => 'Thanh toán đơn hàng bằng ví',
                        ]);
                        $this->orderRepository->update($order->id, ['payment_status' => \App\Enums\Order\PaymentStatus::Paid->value]);
                    } else {
                        DB::rollBack();
                        return false;
                    }
                }

                if (isset($this->data['points'])) {
                    $this->applyPoints($order, $user);
                }
            } else {
                $cart = session()->get('cart', []);
                $cartCollection = collect($cart)->map(function ($item) {
                    return (object) $item;
                });
                $cartCollection = $cartCollection->whereIn('id', $this->data['shopping_cart_id'])->values()->all();
                foreach ($cartCollection as $item) {
                    if ($item->qty >= $this->data['qty'][$item->id]) {
                        $item->qty = $this->data['qty'][$item->id];
                        if (count($cartCollection) === 1) {
                            $isBuyNow = true;
                        }
                    }
                }

                // Kiểm tra số lượng flash sale trước khi tiếp tục
                $this->checkFlashSaleAvailability($cartCollection);

                $this->data['order']['total'] = $this->calculateTotal($cartCollection);
                $this->data['order']['shipping_fee'] = $this->calculateShippingFee(
                    $this->data['order']['province_id'],
                    $this->data['order']['ward_id'],
                    $this->data['order']['total']
                );

                $this->prepareData($cartCollection);
                $this->handleDiscountAndVoucher();
                $order = $this->orderRepository->create($this->data['order']);
                $this->storeOrderDetail($order->id, $this->orderDetails);

                if ($isBuyNow) {
                    foreach ($cart as $key => &$item) {
                        if ($item['id'] == $cartCollection[0]->id) {
                            if ($item['qty'] > $cartCollection[0]->qty) {
                                $item['qty'] -= $cartCollection[0]->qty;
                            } else {
                                unset($cart[$key]);
                            }
                        }
                    }
                    session()->put('cart', array_values($cart));
                    session()->save();
                } else {
                    session()->remove('cart');
                    session()->save();
                }
            }

            // Handle wallet payment for guest carts as well (no points applied)
            if (isset($order) && ($order->payment_method == \App\Enums\Payment\PaymentMethod::Wallet) && $user) {
                $order->refresh();
                $payable = ($order->total + ($order->shipping_fee ?? 0))
                    - (($order->discount_value ?? 0) + ($order->voucher_shipping_discount_value ?? 0) + ($order->voucher_product_discount_value ?? 0));

                if ($user->wallet_balance >= $payable) {
                    $this->userRepository->update($user->id, ['wallet_balance' => $user->wallet_balance - $payable]);
                    \App\Models\WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => -$payable,
                        'type' => 'payment',
                        'status' => 'approved',
                        'order_id' => $order->id,
                        'note' => 'Thanh toán đơn hàng bằng ví',
                    ]);
                    $this->orderRepository->update($order->id, ['payment_status' => \App\Enums\Order\PaymentStatus::Paid->value]);
                } else {
                    DB::rollBack();
                    return false;
                }
            }
            $this->sendOrderNotification($order);
            DB::commit();
            return $order;
        } catch (Exception $e) {
            throw $e;
            $this->logError('Failed to process checkout: ', $e);
            DB::rollBack();
            return false;
        }
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
                'vnp_Amount' => ($order->total - $order->discount_value - $order->points) * 100,
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
            $vnp_Returnurl = route('user.cart.handleVnpayReturn');
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
            header('Location: ' . $vnp_Url);
            die();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
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
                    return to_route('user.getOrderDetailForCustomer', [
                        'code' => $order->code,
                    ])->with('success', __('Thanh toán thành công.'));
                }
            }
            $transaction->update([
                'status' => TransactionStatus::Failed
            ]);
            DB::commit();

            return redirect()->route('user.index')->with('error', __('Thanh toán thất bại'));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
        return $item->product_variation_id
            ? ($item->product->is_flash_sale ? $item->productVariation->flashsale_price * $item->qty : $item->productVariation->promotion_price * $item->qty)
            : ($item->product->is_flash_sale ? $item->product->flashsale_price * $item->qty : $item->product->promotion_price * $item->qty);
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

    public function calculateTotalFromSession($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $product = $this->productRepository->find($item['product_id']);

            if ($item['product_variation_id']) {
                $productVariation = $product->productVariations()->where('id', $item['product_variation_id'])->first();
                $total += $product->is_flash_sale
                    ? $productVariation->flashsale_price * $item['qty']
                    : $productVariation->promotion_price * $item['qty'];
            } else {
                $total += $product->is_flash_sale
                    ? $product->flashsale_price * $item['qty']
                    : $product->promotion_price * $item['qty'];
            }
        }

        return $total;
    }

    protected function storeOrderDetail($orderId, $data)
    {
        foreach ($data as $item) {
            $item['order_id'] = $orderId;
            $this->orderDetailRepository->create($item);
        }
    }

    private function prepareData($cartItems)
    {
        $affiliateList = session()->get('affiliate_list', []);
        $commissionRateSetting = $this->settingRepository->findByField('setting_key', 'commission_rate');

        $commissionRate = $commissionRateSetting ? $commissionRateSetting->plain_value : null;

        foreach ($cartItems as $item) {
            $product = $item->product;

            if ($product->type == ProductType::Simple) {
                $unitPrice = $product->is_flash_sale ? $product->flashsale_price : $product->promotion_price;
            } else {
                $instance = $product->productVariation()->where('id', $item->product_variation_id)->first();
                $unitPrice = $instance->product->is_flash_sale ? $instance->flashsale_price : $instance->promotion_price;
            }

            $affiliateCode = null;
            $affiliateEarning = 0;

            if (!empty($affiliateList) && $commissionRate !== null) {
                $affiliateItem = collect($affiliateList)->firstWhere('slug', $product->slug);

                if ($affiliateItem) {
                    $affiliateCode = $affiliateItem['affiliate_code'] ?? null;
                    $affiliateEarning = $unitPrice * $item->qty * $commissionRate / 100;
                }
            }

            $this->orderDetails[] = [
                'product_id' => $product->id,
                'unit_price' => $unitPrice,
                'product_variation_id' => isset($instance) ? $instance->id : null,
                'affiliate_code' => $affiliateCode,
                'affiliate_earning' => $affiliateEarning,
                'qty' => $item->qty,
                'product_name' => $product->name,
                'product_avatar' => $product->avatar ?? null,
                'product_slug' => $product->slug,
            ];
        }
    }
}
