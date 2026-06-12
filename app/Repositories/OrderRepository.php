<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get filtered and paginated orders
     */
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with('user');

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by payment status
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Confirm order
     */
    public function confirm(int $id): Order
    {
        $order = $this->findOrFail($id);

        if ($order->status !== 'pending') {
            throw new \Exception('Chỉ có thể xác nhận đơn hàng ở trạng thái chờ xử lý');
        }

        $order->update(['status' => 'confirmed']);
        return $order->fresh('user');
    }

    /**
     * Cancel order
     */
    public function cancel(int $id): Order
    {
        $order = $this->findOrFail($id);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            throw new \Exception('Không thể hủy đơn hàng đã hoàn thành hoặc đã hủy');
        }

        $order->update(['status' => 'cancelled']);
        return $order->fresh('user');
    }

    /**
     * Update order status
     */
    public function updateStatus(int $id, string $status): Order
    {
        $order = $this->findOrFail($id);
        $order->update(['status' => $status]);
        return $order->fresh('user');
    }
}
