<?php

namespace App\Api\AdminV1\Http\Controllers\User;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\User\UserRequest;
use App\Api\AdminV1\Http\Resources\User\UserResource;
use App\Api\AdminV1\Http\Resources\User\UserCollection;
use App\Api\AdminV1\Http\Resources\User\PointHistoryResource;
use App\Api\AdminV1\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Api\AdminV1\Repositories\User\UserRepositoryInterface;
use App\Api\AdminV1\Repositories\Order\OrderRepositoryInterface;
use App\Api\AdminV1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Api\AdminV1\Services\User\UserService;
use App\Enums\Order\OrderStatus;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $repository;
    protected $service;
    protected $orderRepository;
    protected $walletTransactionRepository;

    public function __construct(
        UserRepositoryInterface $repository,
        UserService $service,
        OrderRepositoryInterface $orderRepository,
        WalletTransactionRepositoryInterface $walletTransactionRepository
    ) {
        $this->repository = $repository;
        $this->service = $service;
        $this->orderRepository = $orderRepository;
        $this->walletTransactionRepository = $walletTransactionRepository;
    }

    public function index()
    {
        $users = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new UserCollection($users),
        ]);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->get('search')
            ?? $request->get('fullname')
            ?? $request->get('email')
            ?? $request->get('phone');

        $users = $this->repository->search($searchTerm);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new UserCollection($users),
        ]);
    }

    public function store(UserRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $user = $this->service->create($request->validated());
                return new UserResource($user);
            },
            __('user.created_success'),
            201
        );
    }

    public function show(int $id)
    {
        $user = $this->repository->findOrFailWithRelations($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new UserResource($user)
        ]);
    }

    public function update(UserRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $user = $this->service->update($id, $request->validated());
                return new UserResource($user);
            },
            __('user.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('user.deleted_success')
        );
    }

    public function orders(int $id)
    {
        $orders = $this->repository->getUserOrders($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $orders
        ]);
    }

    public function addresses(int $id)
    {
        $addresses = $this->repository->getUserAddresses($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $addresses
        ]);
    }

    /**
     * Lịch sử tích điểm (points_earned)
     */
    public function pointEarnedHistory(Request $request, int $id)
    {
        $user = $this->repository->findOrFailWithRelations($id);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = $this->orderRepository->getQueryBuilder();
        $query = $query->where('user_id', $id)
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

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => PointHistoryResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ]);
    }

    /**
     * Lịch sử dùng điểm (points)
     */
    public function pointUsedHistory(Request $request, int $id)
    {
        $user = $this->repository->findOrFailWithRelations($id);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = $this->orderRepository->getQueryBuilder();
        $query = $query->where('user_id', $id)
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

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => PointHistoryResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ]);
    }

    /**
     * Lịch sử giao dịch ví
     */
    public function walletTransactions(Request $request, int $id)
    {
        $user = $this->repository->findOrFailWithRelations($id);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $query = $this->walletTransactionRepository->getQueryBuilder();
        $query = $query->where('user_id', $id)
            ->orderBy('created_at', 'desc');

        $transactions = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => WalletTransactionResource::collection($transactions->items()),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ]
        ]);
    }
}
