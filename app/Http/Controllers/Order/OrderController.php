<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Services\Order\OrderServiceInterface;
use App\Admin\DataTables\Order\UserOrderDataTable;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\Review\ReviewRepositoryInterface;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Services\Review\ReviewServiceInterface;
use App\Enums\Order\OrderStatus;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Review\ReviewRequest;

class OrderController extends Controller
{
    use AuthService;

    protected $reviewRepository;
    protected $userRepository;
    protected $productRepository;
    protected $orderDetailRepository;
    protected $commissionWithdrawalRepository;

    protected ReviewServiceInterface $reviewService;
    public function __construct(
        OrderRepositoryInterface $repository,
        OrderServiceInterface $service,
        ReviewRepositoryInterface $reviewRepository,
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        CommissionWithdrawalRepositoryInterface $commissionWithdrawalRepository,
        ReviewServiceInterface $reviewService,
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
        $this->reviewRepository = $reviewRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->commissionWithdrawalRepository = $commissionWithdrawalRepository;
        $this->reviewService = $reviewService;
    }
    public function getView(): array
    {
        return [
            'indexUser' => 'user.orders.index',
            'statistical' => 'user.orders.statistical',
            'order-review' => 'user.orders.order-review',
            'register-affiliate' => 'user.orders.register-affiliate',
            'detail' => 'user.orders.order-detail',
            'review' => 'user.orders.review',
        ];
    }

    public function getRoute(): array
    {
        return [];
    }

    public function indexUser(UserOrderDataTable $dataTable)
    {
        return $dataTable->render($this->view['indexUser'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách đơn hàng'))->getBreadcrumbs()
        ]);
    }

    public function statistical()
    {
        $user = $this->getCurrentUser();
        $statistical = $this->repository->statistical($user->id);
        return view($this->view['statistical'], [
            'breadcrumbs' => $this->crums->add(__('Thống kê'))->getBreadcrumbs(),
            'pendingOrders' => $statistical['pendingOrders'],
            'confirmedOrders' => $statistical['confirmedOrders'],
            'deliveringOrders' => $statistical['deliveringOrders'],
            'completedOrders' => $statistical['completedOrders'],
            'cancelledOrders' => $statistical['cancelledOrders'],
            'totalSpent' => $statistical['totalSpent'],
            'monthlySpent' => $statistical['monthlySpent'],
            'months' => $statistical['months'],
        ]);
    }

    public function createReview($id)
    {
        $order = $this->repository->find($id);
        if (!$order || ($order->status != OrderStatus::Completed)) {
            return back()->with('error', __('Không thể đánh giá đơn hàng này.'));
        }

        return view($this->view['order-review'], [
            'instance' => $order,
            'breadcrumbs' => $this->crums->add(__('Danh sách đơn hàng'), route('user.order.indexUser'))->add(__('Đánh giá đơn hàng'))->getBreadcrumbs()
        ]);
    }

    public function showReview($productId, $userId)
    {
        $review = $this->reviewRepository->getBy(['product_id' => $productId, 'user_id' => $userId])->first();
        if ($review) {
            return response()->json(['data' => $review, 'status' => true]);
        }
        return response()->json(['data' => [], 'status' => false]);
    }

    public function storeReview(ReviewRequest $request)
    {
        $response = $this->reviewService->store($request);
        if ($response) {
            return back()->with('success', __('Đánh giá thành công.'));
        }
        return back()->with('error', __('fail'));
    }

    public function updateReview(ReviewRequest $request)
    {
        $response = $this->reviewService->update($request);
        if ($response) {
            return back()->with('success', __('Chỉnh sửa đánh giá thành công.'));
        }
        return back()->with('error', __('fail'));
    }

    public function deleteReview($id)
    {
        $response = $this->reviewService->delete($id);
        if ($response) {
            return back()->with('success', __('Xoá đánh giá thành công.'));
        }
        return back()->with('error', __('fail'));
    }

    public function review_detail($id)
    {
        $reviews = $this->reviewRepository->getQueryBuilder()
            ->where('order_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        $user = $this->userRepository->findOrFail(auth()->id());

        $combinedArray = [];
        foreach ($reviews as $review) {
            $product = $this->productRepository->findOrFail($review->product_id);
            $combinedArray[] = [
                'product_name' => $product->name,
                'review_content' => $review->content,
                'review_rating' => $review->rating,
                'review_created_at' => $review->created_at->format('d-m-Y'),
            ];
        }

        $response = [
            'reviewsDetail' => $combinedArray,
            'user' => [
                'avatar' => asset($user->avatar),
                'fullname' => $user->fullname,
            ],
        ];

        return response()->json(['response' => $response]);
    }

    public function detail($id)
    {
        $instance = $this->repository->findOrFail($id);
        return view($this->view['detail'], [
            'instance' => $instance,
            'breadcrumbs' => $this->crums->add(__('Dach sách đơn hàng'), route('user.order.indexUser'))->add(__('Chi tiết đơn hàng'))->getBreadcrumbs()
        ]);
    }

    public function cancel(CancelOrderRequest $request)
    {
        $result = $this->service->cancel($request);

        if ($result) {
            if (auth('admin')->user()) {
                return to_route('admin.order.index')->with('success', __('Từ chối đơn hàng thành công'));
            } else {
                return to_route('user.order.indexUser')->with('success', __('Hủy đơn hàng thành công'));
            }
        }
        if (auth('admin')->user()) {
            return to_route('admin.order.index')->with('error', __('Từ chối đơn hàng thất bại'));
        } else {
            return to_route('user.order.indexUser')->with('error', __('Hủy đơn hàng thất bại'));
        }
    }
}
