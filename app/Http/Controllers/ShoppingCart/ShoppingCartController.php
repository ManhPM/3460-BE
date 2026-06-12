<?php

namespace App\Http\Controllers\ShoppingCart;

use App\Http\Controllers\Controller;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\ShoppingCart\ShoppingCartServiceInterface;
use App\Admin\Traits\AuthService;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Http\Requests\ShoppingCart\ApplyVoucherRequest;
use App\Http\Requests\ShoppingCart\ChangeQtyRequest;
use App\Http\Requests\ShoppingCart\CheckoutRequest;
use App\Http\Requests\ShoppingCart\ShoppingCartRequest;
use App\Traits\CalculateShippingFee;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    use AuthService, CalculateShippingFee;

    protected DiscountRepositoryInterface $discountRepository;
    protected VoucherRepositoryInterface $voucherRepository;
    protected SettingRepositoryInterface $settingRepository;
    protected OrderRepositoryInterface $orderRepository;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        ProductRepositoryInterface   $repository,
        DiscountRepositoryInterface  $discountRepository,
        VoucherRepositoryInterface  $voucherRepository,
        SettingRepositoryInterface $settingRepository,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        ShoppingCartServiceInterface      $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->discountRepository = $discountRepository;
        $this->settingRepository = $settingRepository;
        $this->orderRepository = $orderRepository;
        $this->voucherRepository = $voucherRepository;
        $this->productRepository = $productRepository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'user.cart.index',
            'voucher' => 'user.vouchers.index',
            'payment' => 'user.cart.payment',
        ];
    }

    public function getRoute(): array
    {
        return [];
    }

    public function voucher()
    {
        return view($this->view['voucher'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách voucher'))->getBreadcrumbs()
        ]);
    }

    public function detailVoucher($id)
    {
        $instance = $this->voucherRepository->find($id);
        if ($instance && $instance->user_id == auth('web')->id()) {
            return view('user.vouchers.detail', [
                'instance' => $instance,
                'breadcrumbs' => $this->crums->add(__('Chi tiết voucher'))->getBreadcrumbs()
            ]);
        } else {
            return redirect()->route('user.index')->with('error', __('Voucher không tồn tại hoặc đã hết hạn sử dụng.'));
        }
    }

    public function index()
    {
        $user = $this->getCurrentUser();

        if ($user) {
            return view($this->view['index'], [
                'shoppingCart' => $user->shopping_cart,
                'total' => $this->service->calculateTotal($user->shopping_cart),
                'breadcrumbs' => $this->crums->add(__('Giỏ hàng'))->getBreadcrumbs()
            ]);
        } else {
            $cart = session()->get('cart', []);
            return view($this->view['index'], [
                'shoppingCart' => $cart,
                'total' => $this->service->calculateTotalFromSession($cart),
                'breadcrumbs' => $this->crums->add(__('Giỏ hàng'))->getBreadcrumbs()
            ]);
        }
    }


    public function checkout(Request $request)
    {
        $user = $this->getCurrentUser();

        if ($user) {
            $defaultAddress = isset($user->addresses[0]) ? $user->addresses[0] : null;
            if ($user->shopping_cart->count() > 0) {
                if ($request->query('cart_id')) {
                    $cartItem = $user->shopping_cart->where('id', $request->query('cart_id'))->first();
                    if ($cartItem) {
                        $cartItem['qty'] = $request->input('qty');
                        $total = $this->service->calculateTotal($cartItem);
                        return view($this->view['payment'], [
                            'user' => $user,
                            'total' => $total,
                            'isBuyNow' => true,
                            'shippingFee' => $defaultAddress ? $this->calculateShippingFee(
                                $defaultAddress->province_id,
                                $defaultAddress->ward_id,
                                $total
                            ) : null,
                            'shoppingCart' => [$cartItem],
                            'payment_methods' => (function () {
                                $methods = PaymentMethod::asSelectArray();
                                return $methods;
                            })(),
                            'admins' => \App\Models\Admin::role('branch')->orderBy('id', 'asc')->get(['id', 'name']),
                            'code' => $request->input('code') ?? null,
                            'breadcrumbs' =>  $this->crums->add(__('Giỏ hàng'), route('user.cart.index'))->add(__('Thanh toán'))->getBreadcrumbs()
                        ]);
                    }
                }
                $total = $this->service->calculateTotal($user->shopping_cart);
                return view($this->view['payment'], [
                    'user' => $user,
                    'total' => $total,
                    'shippingFee' => $defaultAddress ? $this->calculateShippingFee(
                        $defaultAddress->province_id,
                        $defaultAddress->ward_id,
                        $total
                    ) : null,
                    'isBuyNow' => false,
                    'shoppingCart' => $user->shopping_cart,
                    'payment_methods' => (function () {
                        $methods = PaymentMethod::asSelectArray();
                        return $methods;
                    })(),
                    'admins' => \App\Models\Admin::role('branch')->orderBy('id', 'asc')->get(['id', 'name']),
                    'code' => $request->input('code') ?? null,
                    'breadcrumbs' =>  $this->crums->add(__('Giỏ hàng'), route('user.cart.index'))->add(__('Thanh toán'))->getBreadcrumbs()
                ]);
            } else {
                return back()->with('error', __('Giỏ hàng của bạn đang trống.'));
            }
        } else {
            $cart = session()->get('cart', []);
            $cartCollection = collect($cart)->map(function ($item) {
                return (object) $item;
            });
            if ($cartCollection->count() > 0) {
                if ($request->query('cart_id')) {
                    $cartItem = $cartCollection->firstWhere('id', $request->input('cart_id'));
                    if ($cartItem) {
                        $cartItem->qty = $request->input('qty');
                        $total = $this->service->calculateTotal($cartItem);
                        return view($this->view['payment'], [
                            'user' => $user,
                            'total' => $total,
                            'isBuyNow' => true,
                            'shoppingCart' => [$cartItem],
                            'payment_methods' => (function () {
                                $methods = PaymentMethod::asSelectArray();
                                return $methods;
                            })(),
                            'admins' => \App\Models\Admin::role('branch')->orderBy('id', 'asc')->get(['id', 'name']),
                            'code' => $request->input('code') ?? null,
                            'breadcrumbs' =>  $this->crums->add(__('Giỏ hàng'), route('user.cart.index'))->add(__('Thanh toán'))->getBreadcrumbs()
                        ]);
                    }
                }
                $total = $this->service->calculateTotal($cartCollection);
                return view($this->view['payment'], [
                    'total' => $total,
                    'isBuyNow' => false,
                    'shoppingCart' => $cartCollection,
                    'payment_methods' => (function () {
                        $methods = PaymentMethod::asSelectArray();
                        return $methods;
                    })(),
                    'admins' => \App\Models\Admin::role('branch')->orderBy('id', 'asc')->get(['id', 'name']),
                    'code' => $request->input('code') ?? null,
                    'breadcrumbs' =>  $this->crums->add(__('Giỏ hàng'), route('user.cart.index'))->add(__('Thanh toán'))->getBreadcrumbs()
                ]);
            } else {
                return back()->with('error', __('Giỏ hàng của bạn đang trống.'));
            }
        }
    }

    public function checkoutFinal(CheckoutRequest $request)
    {
        $order = $this->service->checkout($request);

        if ($order) {
            $message = __('Đặt hàng thành công, hãy kiểm tra thông tin đơn hàng của bạn.'); // Thông báo mặc định

            return to_route('user.getOrderDetailForCustomer', [
                'code' => $order->code,
                'phone' => $order->phone,
            ])->with('success', $message);
        }

        return back()->with('error', __('Đặt hàng thất bại'));
    }


    public function prepareDataVnpay($code)
    {
        $order = $this->orderRepository->findByField('code', $code);
        return view('user.home.create-payment-vnpay', [
            'order' => $order,
            'breadcrumbs' =>  $this->crums->add(__('Giỏ hàng'), route('user.cart.index'))->add(__('Thanh toán'))->getBreadcrumbs()
        ]);
        if ($order) {
            if ($order->payment_method == PaymentMethod::VNPAY && $order->payment_status == PaymentStatus::Unpaid->value) {
                return view('user.home.create-payment-vnpay', [
                    'order' => $order,
                    'breadcrumbs' =>  $this->crums->add(__('Giỏ hàng'), route('user.cart.index'))->add(__('Thanh toán'))->getBreadcrumbs()
                ]);
            }
        }
        return back()->with('error', __('Thực hiện không thành công.'));
    }

    public function handleVnpay(Request $request)
    {
        return $this->service->handleVnpay($request);
    }

    public function handleVnpayReturn(Request $request)
    {
        return $this->service->handleVnpayReturn($request);
    }

    public function store(ShoppingCartRequest $request)
    {
        $user = $this->getCurrentUser();
        if ($user) {
            $result = $this->service->store($request);
            if ($result === 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Thêm sản phẩm thất bại, số lượng có thể mua đã đạt tối đa',
                ], 400);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'total' => $this->service->calculateTotal($user->shopping_cart),
                    'count' => $user->shopping_cart()->sum('qty'),
                ]
            ]);
        } else {
            $result = $this->service->storeNotLogin($request);
            if ($result === 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Thêm sản phẩm thất bại, số lượng có thể mua đã đạt tối đa',
                ], 400);
            }
            $cart = session()->get('cart', []);
            $count = 0;
            foreach ($cart as $item) {
                $count += $item['qty'];
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'count' => $count,
                ]
            ]);
        }
    }


    public function buyNow(ShoppingCartRequest $request)
    {
        $user = $this->getCurrentUser();

        if ($user) {
            $result = $this->service->store($request);
            if ($result === 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Thêm sản phẩm thất bại, số lượng có thể mua đã đạt tối đa',
                ], 400);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $result->id,
                    'qty' => $request->input('qty')
                ]
            ], 200);
        } else {
            $result = $this->service->storeNotLogin($request);
            if ($result === 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Thêm sản phẩm thất bại, số lượng có thể mua đã đạt tối đa',
                ], 400);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $result->id,
                    'qty' => $request->input('qty')
                ]
            ], 200);
        }
    }


    public function applyDiscountCode(ApplyVoucherRequest $request)
    {
        $cart = session()->get('cart', []);
        $cartCollection = collect($cart)->map(function ($item) {
            return (object) $item;
        });
        if ($request->input('cart_id')) {
            if ($cartCollection) {
                $cartCollection = $cartCollection->firstWhere('id', $request->input('cart_id'));
                $cartCollection->qty = $request->input('qty');
                $total = $this->service->calculateTotal($cartCollection);
                $discount = $this->discountRepository->findByField('code', $request->input('code'));
                if ($discount) {
                    if ($total < $discount->min_order_amount || $discount->max_usage <= 0) {
                        return response()->json([
                            'status' => false,
                            'data' => [
                                'message' => 'Mã giảm giá đã hết hoặc Đơn hàng chưa đủ điều kiện sử dụng mã giảm giá này. Giá trị đơn hàng hiện tại: '
                                    . $total . ', giá trị đơn hàng đủ điều kiện: '
                                    . $discount->min_order_amount . '.',
                                'total' => $total,
                                'shipping_fee' => (int) $this->calculateShippingFeeFromRequest($request, $total),
                                'discount_value' => 0,
                                'shipping_discount_value' => 0,
                                'voucher_discount_value' => 0,
                            ]
                        ], 400);
                    }
                    return response()->json([
                        'status' => true,
                        'data' => [
                            'total' => $total,
                            'discount_value' => (int) $this->service->calculateDiscountValue($total, $discount),
                            'shipping_fee' => (int) $this->calculateShippingFeeFromRequest($request, $total),
                            'shipping_discount_value' => 0,
                            'voucher_discount_value' => 0,
                        ]
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'discount_value' => 0,
                        'shipping_fee' => (int) $this->calculateShippingFeeFromRequest($request, $total),
                        'shipping_discount_value' => 0,
                        'voucher_discount_value' => 0,
                    ]
                ]);
            }
            return response()->json([
                'status' => false,
                'data' => [
                    'message' => 'Giỏ hàng không tồn tại!',
                ]
            ], 400);
        } else {
            $total = $this->service->calculateTotal($cartCollection);
            $discount = $this->discountRepository->findByField('code', $request->input('code'));
            if ($discount) {
                if ($total < $discount->min_order_amount || $discount->max_usage <= 0) {
                    return response()->json([
                        'status' => false,
                        'data' => [
                            'message' => 'Mã giảm giá đã hết hoặc Đơn hàng chưa đủ điều kiện sử dụng mã giảm giá này. Giá trị đơn hàng hiện tại: '
                                . $total . ', giá trị đơn hàng đủ điều kiện: '
                                . $discount->min_order_amount . '.',
                            'total' => $total,
                            'shipping_fee' => (int) $this->calculateShippingFeeFromRequest($request, $total),
                            'discount_value' => 0,
                            'shipping_discount_value' => 0,
                            'voucher_discount_value' => 0,
                        ]
                    ], 400);
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'discount_value' => (int) $this->service->calculateDiscountValue($total, $discount),
                        'shipping_fee' => (int) $this->calculateShippingFeeFromRequest($request, $total),
                        'shipping_discount_value' => 0,
                        'voucher_discount_value' => 0,
                    ]
                ]);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'total' => $total,
                    'discount_value' => 0,
                    'shipping_fee' => (int) $this->calculateShippingFeeFromRequest($request, $total),
                    'shipping_discount_value' => 0,
                    'voucher_discount_value' => 0,
                ]
            ]);
        }
    }

    private function calculateShippingFeeFromRequest($request, $total)
    {
        if ($request->input('province_id') && $request->input('ward_id')) {
            return $request->input('province_id') ? $this->calculateShippingFee(
                $request->input('province_id'),
                $request->input('ward_id'),
                $total
            ) : 0;
        }
        return -1;
    }

    private function errorResponse($message, $total = 0)
    {
        return response()->json([
            'status' => false,
            'data' => [
                'message' => $message,
                'total' => $total,
                'shipping_fee' => 0,
                'discount_value' => 0,
                'shipping_discount_value' => 0,
                'voucher_discount_value' => 0,
            ]
        ], 400);
    }

    private function successResponse($total, $discount, $voucherShipping, $voucherProduct, $shippingFee)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'total' => $total,
                'shipping_fee' => (int) $shippingFee,
                'discount_value' => $discount ? $this->service->calculateDiscountValue($total, $discount) : 0,
                'shipping_discount_value' => $voucherShipping ? $this->service->calculateShippingDiscountValue($total, $voucherShipping, $shippingFee) : 0,
                'voucher_discount_value' => $voucherProduct ? $this->service->calculateDiscountValue($total, $voucherProduct) : 0,
            ]
        ]);
    }

    private function getVoucher($voucherId)
    {
        return $voucherId ? $this->voucherRepository->find($voucherId) : null;
    }

    private function isDiscountInvalid($total, $discount, $voucherShipping, $voucherProduct)
    {
        return ($discount && ($total < $discount->min_order_amount)) ||
            ($discount && ($discount->max_usage <= 0)) ||
            ($voucherShipping && ($total < $voucherShipping->min_order_amount)) ||
            ($voucherProduct && ($total < $voucherProduct->min_order_amount));
    }

    public function applyVoucher(ApplyVoucherRequest $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return $this->applyDiscountCode($request);
        }
        $discount = $this->discountRepository->findByField('code', $request->input('code'));
        $voucherShipping = $this->getVoucher($request->input('voucher_shipping_id'));
        $voucherProduct = $this->getVoucher($request->input('voucher_product_id'));
        if ($request->input('cart_id')) {
            $shoppingCart = $user->shopping_cart()->find($request->input('cart_id'));

            if (!$shoppingCart) {
                return $this->errorResponse('Giỏ hàng không tồn tại!');
            }

            $shoppingCart['qty'] = $request->input('qty');
            $total = $this->service->calculateTotal($shoppingCart);
            $shippingFee = $this->calculateShippingFeeFromRequest($request, $total);

            if ($this->isDiscountInvalid($total, $discount, $voucherShipping, $voucherProduct)) {
                return $this->errorResponse('Mã giảm giá hoặc các voucher đang chọn đã hết hoặc Đơn hàng chưa đủ điều kiện sử dụng các ưu đãi này.', $total);
            }

            return $this->successResponse($total, $discount, $voucherShipping, $voucherProduct, $shippingFee);
        } else {
            $total = $this->service->calculateTotal($user->shopping_cart);
            $discount = $this->discountRepository->findByField('code', $request->input('code'));

            if ($this->isDiscountInvalid($total, $discount, $voucherShipping, $voucherProduct)) {
                return $this->errorResponse('Mã giảm giá hoặc các voucher đang chọn đã hết hoặc Đơn hàng chưa đủ điều kiện sử dụng các ưu đãi này.', $total);
            }
            $shippingFee = $this->calculateShippingFeeFromRequest($request, $total);

            return $this->successResponse($total, $discount, $voucherShipping, $voucherProduct, $shippingFee);
        }
    }

    public function increament(ChangeQtyRequest $request)
    {
        $result = $this->service->increament($request);
        if ($result === 1) {
            return response()->json([
                'status' => false,
                'message' => 'Số lượng có thể mua đã đạt tối đa.!'
            ], 400);
        }
        if ($result) {
            if ($this->getCurrentUser()) {
                $user = $this->getCurrentUser();
                $total = $this->service->calculateTotal($user->shopping_cart);
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $user->shopping_cart()->sum('qty'),
                    ]
                ]);
            } else {
                $cart = session()->get('cart', []);
                $cartCollection = collect($cart)->map(function ($item) {
                    return (object) $item;
                });
                $total = $this->service->calculateTotal($cartCollection);
                $count = 0;
                foreach ($cart as $item) {
                    $count += $item['qty'];
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $count,
                    ]
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Tăng số lượng thất bại!'
        ], 400);
    }

    public function decreament(ChangeQtyRequest $request)
    {
        $result = $this->service->decreament($request);
        if ($result) {
            if ($this->getCurrentUser()) {
                $user = $this->getCurrentUser();
                $total = $this->service->calculateTotal($user->shopping_cart);
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $user->shopping_cart()->sum('qty'),
                    ]
                ]);
            } else {
                $cart = session()->get('cart', []);
                $cartCollection = collect($cart)->map(function ($item) {
                    return (object) $item;
                });
                $total = $this->service->calculateTotal($cartCollection);
                $count = 0;
                foreach ($cart as $item) {
                    $count += $item['qty'];
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $count,
                    ]
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Giảm số lượng thất bại!'
            ], 400);
        }
    }

    public function update(ChangeQtyRequest $request)
    {
        $result = $this->service->update($request);
        if ($result === 1) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật giỏ hàng thất bại. Số lượng hàng còn lại không đủ!'
            ], 400);
        }
        if ($result) {
            if ($this->getCurrentUser()) {
                $user = $this->getCurrentUser();
                $total = $this->service->calculateTotal($user->shopping_cart);
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $user->shopping_cart()->sum('qty'),
                    ]
                ]);
            } else {
                $cart = session()->get('cart', []);
                $cartCollection = collect($cart)->map(function ($item) {
                    return (object) $item;
                });
                $total = $this->service->calculateTotal($cartCollection);
                $count = 0;
                foreach ($cart as $item) {
                    $count += $item['qty'];
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $count,
                    ]
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật giỏ hàng thất bại.'
            ], 400);
        }
    }

    public function delete($id)
    {
        $result = $this->service->delete($id);
        if ($result) {
            if ($this->getCurrentUser()) {
                $user = $this->getCurrentUser();
                $total = $this->service->calculateTotal($user->shopping_cart);
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $user->shopping_cart()->sum('qty'),
                    ]
                ]);
            } else {
                $cart = session()->get('cart', []);
                $cartCollection = collect($cart)->map(function ($item) {
                    return (object) $item;
                });
                $total = $this->service->calculateTotal($cartCollection);
                $count = 0;
                foreach ($cart as $item) {
                    $count += $item['qty'];
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'total' => $total,
                        'count' => $count,
                    ]
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật giỏ hàng thất bại.'
            ], 400);
        }
    }

    public function getCartItems()
    {
        $user = $this->getCurrentUser();
        if ($user) {
            $cart = $user->shopping_cart;
        } else {
            $cart = session()->get('cart', []);
        }

        if (!isset($cart[0])) {
            return response()->json([
                'cart_items' => [],
                'cart_total' => 0,
            ]);
        }

        $cartItems = [];
        foreach ($cart as $item) {
            $product = $item['product'];
            if (!$user) {
                $product = $this->productRepository->find($item['product']['id']);
            }
            $productVariation = $item['productVariation'] ?? null;

            // Kiểm tra giá dựa vào flash sale
            if ($productVariation) {
                $price = $product->is_flash_sale
                    ? $productVariation['flashsale_price']
                    : $productVariation['promotion_price'];
            } else {
                $price = $product->is_flash_sale
                    ? $product['flashsale_price']
                    : $product['promotion_price'];
            }

            // Lấy thuộc tính nếu có product variation
            $attributes = $productVariation
                ? $productVariation['attributeVariations']->pluck('name')->toArray()
                : [];

            $cartItems[] = [
                'id' => $item['id'],
                'name' => $product['name'],
                'price' => $price,
                'quantity' => $item['qty'],
                'image' => asset($product['avatar']),
                'total_price' => $price * $item['qty'],
                'attributes' => $attributes,
            ];
        }

        $cartTotal = array_reduce($cartItems, fn($carry, $item) => $carry + $item['total_price'], 0);

        return response()->json([
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal,
        ]);
    }
}
