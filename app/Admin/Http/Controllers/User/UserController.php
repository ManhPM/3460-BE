<?php

namespace App\Admin\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\User\UserRequest;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\User\UserServiceInterface;
use App\Admin\DataTables\User\UserDataTable;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Enums\User\{Gender};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected OrderDetailRepositoryInterface $orderDetailRepository;
    protected CommissionWithdrawalRepositoryInterface $commissionWithdrawalRepository;
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(
        UserRepositoryInterface $repository,
        UserServiceInterface    $service,
        OrderDetailRepositoryInterface $orderDetailRepository,
        CommissionWithdrawalRepositoryInterface $commissionWithdrawalRepository,
        OrderRepositoryInterface $orderRepository,
    ) {

        parent::__construct();

        $this->repository = $repository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->commissionWithdrawalRepository = $commissionWithdrawalRepository;
        $this->orderRepository = $orderRepository;

        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'edit' => 'admin.users.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.user.index',
            'create' => 'admin.user.create',
            'edit' => 'admin.user.edit',
            'delete' => 'admin.user.delete'
        ];
    }

    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'gender' => Gender::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh sách khách hàng'))
        ]);
    }

    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách khách hàng'), route($this->route['index']))->add(__('add')),
            [
                'gender' => Gender::asSelectArray(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách khách hàng'), route($this->route['index']))->add(__('edit')),
            [
                'instance' => $instance,
                'gender' => Gender::asSelectArray(),
            ]
        );
    }


    public function store(UserRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(UserRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }

    /**
     * Lịch sử tích điểm (points_earned)
     */
    public function pointEarnedHistory(Request $request, $userId): JsonResponse
    {
        $user = $this->repository->findOrFail($userId);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = $this->orderRepository->getQueryBuilder();
        $query = $query->where('user_id', $userId)
            ->where('points_earned', '>', 0)
            ->where('status', OrderStatus::Completed)
            ->orderBy('created_at', 'desc');

        // Filter by search (order code)
        if ($search !== '') {
            $query->where('code', 'like', "%{$search}%");
        }

        // Filter by date
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate($perPage, ['*'], 'page', $page);

        $html = view('admin.users.partials.point-earned-history', [
            'orders' => $orders,
            'user' => $user
        ])->render();

        return response()->json([
            'html' => $html,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Lịch sử dùng điểm (points)
     */
    public function pointUsedHistory(Request $request, $userId): JsonResponse
    {
        $user = $this->repository->findOrFail($userId);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = $this->orderRepository->getQueryBuilder();
        $query = $query->where('user_id', $userId)
            ->where('points', '>', 0)
            ->orderBy('created_at', 'desc');

        // Filter by search (order code)
        if ($search !== '') {
            $query->where('code', 'like', "%{$search}%");
        }

        // Filter by date
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate($perPage, ['*'], 'page', $page);

        $html = view('admin.users.partials.point-used-history', [
            'orders' => $orders,
            'user' => $user
        ])->render();

        return response()->json([
            'html' => $html,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }
}
