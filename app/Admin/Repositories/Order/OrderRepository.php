<?php

namespace App\Admin\Repositories\Order;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;

class OrderRepository extends EloquentRepository implements OrderRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Order::class;
    }
    public function findOrFailWithRelations($id, array $relations = ['details', 'user'])
    {
        $this->findOrFail($id);
        $this->instance = $this->instance->load($relations);
        return $this->instance;
    }
    public function getQueryBuilderWithRelations($relations = ['user'])
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->with($relations)->orderBy('id', 'desc');
        return $this->instance;
    }

    public function statistical($userId = null)
    {
        $orderQuery = Order::where('user_id', $userId);

        // Thống kê số lượng đơn hàng theo trạng thái
        $statusCounts = $orderQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Tổng số tiền đã chi tiêu
        $totalSpent = Order::where('user_id', $userId)->where('status', '!=', OrderStatus::Cancelled)->sum('total');

        // Lấy năm hiện tại
        $currentYear = Carbon::now()->year;

        // Lấy danh sách 12 tháng
        $months = collect(range(1, 12))->map(fn($month) => Carbon::create($currentYear, $month, 1)->format('M'));

        // Chi tiêu theo từng tháng
        $months = collect([
            'Tháng 1',
            'Tháng 2',
            'Tháng 3',
            'Tháng 4',
            'Tháng 5',
            'Tháng 6',
            'Tháng 7',
            'Tháng 8',
            'Tháng 9',
            'Tháng 10',
            'Tháng 11',
            'Tháng 12'
        ]);

        $monthlySpent = $months->mapWithKeys(function ($month, $index) use ($userId, $currentYear) {
            $total = Order::where('user_id', $userId)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $index + 1)
                ->where('status', '!=', OrderStatus::Cancelled)
                ->sum('total');

            return [$month => $total];
        });

        return [
            'pendingOrders' => $statusCounts[OrderStatus::Pending] ?? 0,
            'confirmedOrders' => $statusCounts[OrderStatus::Confirmed] ?? 0,
            'deliveringOrders' => $statusCounts[OrderStatus::Delivering] ?? 0,
            'completedOrders' => $statusCounts[OrderStatus::Completed] ?? 0,
            'cancelledOrders' => $statusCounts[OrderStatus::Cancelled] ?? 0,
            'totalSpent' => $totalSpent,
            'monthlySpent' => $monthlySpent,
            'months' => $months
        ];
    }
}
