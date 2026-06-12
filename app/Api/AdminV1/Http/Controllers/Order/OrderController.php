<?php

namespace App\Api\AdminV1\Http\Controllers\Order;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Order\OrderRequest;
use App\Api\AdminV1\Http\Resources\Order\OrderResource;
use App\Api\AdminV1\Http\Resources\Order\OrderCollection;
use App\Api\AdminV1\Repositories\Order\OrderRepositoryInterface;
use App\Api\AdminV1\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $service;

    public function __construct(
        OrderRepositoryInterface $repository,
        OrderService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $orders = $this->repository->getFiltered();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new OrderCollection($orders),
        ]);
    }

    public function store(OrderRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                // Check valid discount first
                if (!$this->service->checkValidDiscount($request)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Hóa đơn không đủ điều kiện để nhập mã giảm giá hiện tại',
                    ], 422);
                }

                $order = $this->service->store($request);
                if (!$order) {
                    return response()->json([
                        'status' => 422,
                        'message' => __('order.create_failed'),
                    ], 422);
                }
                return new OrderResource($order->load(['user', 'admin', 'province', 'ward', 'details.product', 'details.productVariation.attributeVariations']));
            },
            __('order.created_success')
        );
    }

    public function show(int $id)
    {
        $order = $this->repository->findOrFailWithRelations($id);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new OrderResource($order)
        ]);
    }

    public function update(OrderRequest $request, int $id)
    {
        // Merge route id into request data
        $request->merge([
            'order' => array_merge($request->input('order', []), ['id' => $id])
        ]);

        return $this->handleUpdateResponse(
            $request,
            function ($request) {
                $order = $this->service->update($request);
                if (!$order) {
                    return response()->json([
                        'status' => 422,
                        'message' => __('order.update_failed'),
                    ], 422);
                }
                return new OrderResource($order->load(['user', 'admin', 'province', 'ward', 'details.product', 'details.productVariation.attributeVariations']));
            },
            __('order.updated_success')
        );
    }

    public function confirm(int $id)
    {
        try {
            $order = $this->service->confirm($id);

            if (!$order) {
                return response()->json([
                    'status' => 422,
                    'message' => __('order.confirm_failed'),
                ], 422);
            }

            return response()->json([
                'status' => 200,
                'message' => __('order.confirmed_success'),
                'data' => new OrderResource($order->load(['user', 'admin', 'province', 'ward', 'details.product', 'details.productVariation.attributeVariations'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function cancel(Request $request, int $id)
    {
        try {
            $cancelReason = $request->input('cancel_reason', '');
            $order = $this->service->cancel($id, $cancelReason);

            if (!$order) {
                return response()->json([
                    'status' => 422,
                    'message' => __('order.cancel_failed'),
                ], 422);
            }

            return response()->json([
                'status' => 200,
                'message' => __('order.cancelled_success'),
                'data' => new OrderResource($order->load(['user', 'admin', 'province', 'ward', 'details.product', 'details.productVariation.attributeVariations'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => $e->getMessage() ?: __('order.cancel_failed'),
            ], 422);
        }
    }

    public function updateStatus(OrderRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $order = $this->service->updateStatus($id, $request->validated()['order']['status'] ?? $request->validated()['status']);
                return new OrderResource($order);
            },
            __('order.status_updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $result = $this->service->delete($id);
                if (!$result) {
                    return response()->json([
                        'status' => 422,
                        'message' => __('order.delete_failed'),
                    ], 422);
                }
                return null;
            },
            __('order.deleted_success')
        );
    }
}
