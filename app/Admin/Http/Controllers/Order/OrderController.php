<?php

namespace App\Admin\Http\Controllers\Order;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Services\Order\OrderServiceInterface;
use App\Admin\DataTables\Order\OrderDataTable;
use App\Admin\DataTables\Order\UserOrderDataTable;
use App\Enums\Order\OrderStatus;
use App\Admin\Http\Requests\Order\OrderRequest;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Admin\Repositories\Product\{ProductRepositoryInterface, ProductVariationRepositoryInterface};
use App\Admin\Traits\AuthService;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Traits\CalculateShippingFee;

class OrderController extends Controller
{
    use AuthService, CalculateShippingFee;
    protected UserRepositoryInterface $repositoryUser;
    protected ProductRepositoryInterface $repositoryProduct;
    protected DiscountRepositoryInterface $discountRepository;
    protected ProductVariationRepositoryInterface $repositoryProductVariation;

    public function __construct(
        OrderRepositoryInterface $repository,
        UserRepositoryInterface $repositoryUser,
        ProductRepositoryInterface $repositoryProduct,
        DiscountRepositoryInterface $discountRepository,
        ProductVariationRepositoryInterface $repositoryProductVariation,
        OrderServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->repositoryUser = $repositoryUser;
        $this->discountRepository = $discountRepository;
        $this->repositoryProduct = $repositoryProduct;
        $this->repositoryProductVariation = $repositoryProductVariation;
        $this->service = $service;
    }
    public function getView(): array
    {
        return [
            'index' => 'admin.orders.index',
            'indexUser' => 'user.orders.index',
            'detail' => 'user.orders.order-detail',
            'create' => 'admin.orders.create',
            'edit' => 'admin.orders.edit',
            'info_shipping' => 'admin.orders.partials.info-shipping',
            'add_item_product' => 'admin.orders.partials.add-item-product',
            'total' => 'admin.orders.partials.total'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.order.index',
            'create' => 'admin.order.create',
            'edit' => 'admin.order.edit',
            'delete' => 'admin.order.delete',
        ];
    }

    public function indexUser(UserOrderDataTable $dataTable)
    {
        return $dataTable->render($this->view['indexUser'], []);
    }

    public function detail($id)
    {
        $instance = $this->repository->findOrFail($id);
        // Abort if branch admin tries to access other admin's order
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            if ((int)$instance->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }
        return view($this->view['detail'], [
            'instance' => $instance
        ]);
    }

    public function index(OrderDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách đơn hàng'))
        ]);
    }

    public function store(OrderRequest $request): RedirectResponse
    {
        try {
            $result = $this->service->checkValidDiscount($request);
            if ($result) {
                $order = $this->service->store($request);
                if ($order) {
                    return to_route($this->route['edit'], $order->id)->with('success', __('success'));
                }
                return back()->with('error', __('fail'));
            }
            return back()->with('error', __('Hóa đơn không đủ điều kiện để nhập mã giảm giá hiện tại'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách đơn hàng'), route($this->route['index']))->add(__('add')),
            [
                'payment_methods' => PaymentMethod::asSelectArray(),
                'payment_statuses' => PaymentStatus::asSelectArray(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        // Abort if branch admin tries to access other admin's order
        $admin = auth('admin')->user();
        $order = $this->repository->findOrFail($id);
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            if ((int)$order->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách đơn hàng'), route($this->route['index']))->add(__('edit')),
            [
                'order' => $this->repository->findOrFailWithRelations($id),
                'status' => OrderStatus::asSelectArray(),
                'payment_methods' => PaymentMethod::asSelectArray(),
                'payment_statuses' => PaymentStatus::asSelectArray(),
            ]
        );
    }

    public function update(OrderRequest $request): RedirectResponse
    {
        try {
            $result = $this->service->checkValidDiscount($request);
            if ($result) {
                $response = $this->service->update($request);
                if ($response) {
                    return back()->with('success', __('success'));
                }
                return back()->with('error', __('fail'));
            }
            return back()->with('error', __('Hóa đơn không đủ điều kiện để nhập mã giảm giá hiện tại'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete($id): RedirectResponse
    {
        // Abort if branch admin tries to delete other admin's order
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            $order = $this->repository->findOrFail($id);
            if ((int)$order->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }

        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }

    public function renderInfoShipping(OrderRequest $request): Factory|View|Application
    {
        $user = $this->repositoryUser->findOrFail($request->input('user_id'));
        return view($this->view['info_shipping'], [
            'customer_fullname' => $user->fullname,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'shipping_address' => $user->address
        ]);
    }

    public function confirm($id)
    {
        // Abort if branch admin tries to confirm other admin's order
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            $order = $this->repository->findOrFail($id);
            if ((int)$order->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }

        $result = $this->service->confirm($id);
        if ($result === 1) {
            return to_route($this->route['index'])->with('error', __('Đơn hàng chứa sản phẩm không còn flash sale -> cần hủy'));
        }
        if ($result !== true && $result !== false) {
            return to_route($this->route['index'])->with('error', __('Không đủ sản phẩm: ' . $result));
        }
        if ($result) {
            return to_route($this->route['index'])->with('success', __('Duyệt đơn hàng thành công'));
        }
        return to_route($this->route['index'])->with('error', __('Duyệt đơn hàng thất bại'));
    }

    public function cancel($id)
    {
        // Abort if branch admin tries to cancel other admin's order
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            $order = $this->repository->findOrFail($id);
            if ((int)$order->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }

        $result = $this->service->cancel($id);
        if ($result) {
            return to_route($this->route['index'])->with('success', __('Từ chối đơn hàng thành công'));
        }
        return to_route($this->route['index'])->with('error', __('Từ chối đơn hàng thất bại'));
    }

    public function addProduct(OrderRequest $request): JsonResponse
    {

        $product = $this->service->addProduct($request);

        if (!$product) {
            return response()->json([
                'status' => 400,
                'message' => __('fail')
            ], 400);
        }
        $response = view($this->view['add_item_product'], compact('product'))->render();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $response
        ], 200);
    }

    public function calculateTotalBeforeSaveOrder(OrderRequest $request): JsonResponse
    {
        $total = 0;
        $shipping_fee = $request->input('order.shipping_fee', 0);;
        $voucher_shipping_discount_value = $request->input('order.voucher_shipping_discount_value', 0);;
        $voucher_product_discount_value = $request->input('order.voucher_product_discount_value', 0);;
        $points = $request->input('order.points', 0);
        $discountValue = $request->input('order.discount_value', 0);

        $settingRepository = app()->make(\App\Admin\Repositories\Setting\SettingRepository::class);
        $settings = $settingRepository->getAll();
        $exchangePercent = $settings->where('setting_key', 'exchange_percent')->first()->plain_value;
        $points_discount_value = $points * $exchangePercent;

        if ($request->input('order_detail.product_slug')) {
            $total = $this->service->calculateTotal($request);
            if ($request->input('order.discount_id')) {
                $discountId = $request->input('order.discount_id');
                $discount = $this->discountRepository->findOrFail($discountId);
                if ($total >= $discount->min_order_amount) {
                    if ($discount->type == DiscountValueType::Money) {
                        $discountValue = $discount->discount_value;
                    } else {
                        $discountValue = $total * $discount->discount_value / 100;
                    }
                }
            }
        }

        $final_total = max(0, $total + $shipping_fee - $discountValue - $voucher_shipping_discount_value - $voucher_product_discount_value - $points_discount_value);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => view($this->view['total'], [
                'total' => $total,
                'shipping_fee' => $shipping_fee,
                'points' => $points,
                'discountValue' => $discountValue,
                'voucher_shipping_discount_value' => $voucher_shipping_discount_value,
                'voucher_product_discount_value' => $voucher_product_discount_value,
                'points_discount_value' => $points_discount_value,
                'final_total' => $final_total,
            ])->render()
        ], 200);
    }
}
