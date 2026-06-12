<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $orders = $this->orderRepository->getFiltered($request->all());
        return response()->json($orders);
    }

    /**
     * Display the specified order
     */
    public function show(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order->load(['user', 'items.product'])
        ]);
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,processing,shipping,completed,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed',
            'note' => 'nullable|string',
        ]);

        $this->orderRepository->update($id, $validated);
        $order = $this->orderRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order->load('user'),
            'message' => 'Cập nhật đơn hàng thành công'
        ]);
    }

    /**
     * Confirm order
     */
    public function confirm(int $id)
    {
        try {
            $order = $this->orderRepository->confirm($id);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Xác nhận đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(int $id)
    {
        try {
            $order = $this->orderRepository->cancel($id);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Hủy đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipping,completed,cancelled',
        ]);

        $order = $this->orderRepository->updateStatus($id, $validated['status']);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Cập nhật trạng thái đơn hàng thành công'
        ]);
    }
}
