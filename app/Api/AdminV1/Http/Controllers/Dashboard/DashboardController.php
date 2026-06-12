<?php

namespace App\Api\AdminV1\Http\Controllers\Dashboard;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Enums\DefaultActiveStatus;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $fromDate = request('from_date');
        $toDate = request('to_date');

        $orderQuery = Order::query()->where('is_deleted', 0);
        $userQuery = User::query();
        $orderDetailQuery = OrderDetail::query()->whereHas('order', fn($q) => $q->where('is_deleted', 0));

        if (!isSuperAdmin()) {
            $orderQuery->where('admin_id', auth()->user()->id);
            $orderDetailQuery->whereHas('order', fn($q) => $q->where('admin_id', auth()->user()->id));
        }

        if ($fromDate) {
            $orderQuery->whereDate('created_at', '>=', $fromDate);
            $userQuery->whereDate('created_at', '>=', $fromDate);
            $orderDetailQuery->whereHas('order', fn($q) => $q->whereDate('created_at', '>=', $fromDate));
        }
        if ($toDate) {
            $orderQuery->whereDate('created_at', '<=', $toDate);
            $userQuery->whereDate('created_at', '<=', $toDate);
            $orderDetailQuery->whereHas('order', fn($q) => $q->whereDate('created_at', '<=', $toDate));
        }

        $totalOrders = (clone $orderQuery)->count();
        $pendingOrders = (clone $orderQuery)->where('status', OrderStatus::Pending)->count();
        $completedOrders = (clone $orderQuery)->where('status', OrderStatus::Completed)->count();
        $totalRevenue = (clone $orderQuery)->where('status', OrderStatus::Completed)->sum('total');
        $totalCustomers = (clone $userQuery)->count();

        // Customers
        if ($fromDate || $toDate) {
            $newCustomers = (clone $userQuery)->count();
            $newCustomersThisYear = (clone $userQuery)->count();
        } else {
            $newCustomers = User::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
            $newCustomersThisYear = User::whereYear('created_at', Carbon::now()->year)->count();
        }

        // Monthly revenue labels and data
        $months = [];
        $monthlyRevenue = [];
        if ($fromDate || $toDate) {
            $start = $fromDate ? Carbon::parse($fromDate)->startOfMonth() : Carbon::now()->startOfYear();
            $end = $toDate ? Carbon::parse($toDate)->endOfMonth() : Carbon::now()->endOfYear();
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $months[] = 'Tháng ' . $cursor->month;
                $monthlyRevenue[] = Order::whereBetween('created_at', [$cursor->copy()->startOfMonth(), $cursor->copy()->endOfMonth()])
                    ->where('status', OrderStatus::Completed)
                    ->sum('total');
                $cursor->addMonth();
            }
        } else {
            for ($i = 0; $i < 12; $i++) {
                $labelMonth = Carbon::now()->startOfYear()->addMonths($i);
                $months[] = 'Tháng ' . $labelMonth->month;
                $monthlyRevenue[] = Order::whereYear('created_at', $labelMonth->year)
                    ->whereMonth('created_at', $labelMonth->month)
                    ->where('status', OrderStatus::Completed)
                    ->sum('total');
            }
        }

        $totalProductsSold = (clone $orderDetailQuery)->whereHas('order', function ($query) {
            $query->where('status', OrderStatus::Completed);
        })->sum('qty');

        $cancelledOrders = (clone $orderQuery)->where('status', OrderStatus::Cancelled)->count();
        $cancelRate = $totalOrders > 0 ? ($cancelledOrders / $totalOrders) * 100 : 0;

        $averageOrderValue = $completedOrders > 0
            ? $totalRevenue / $completedOrders
            : 0;

        $returningCustomersCount = (clone $orderQuery)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id')
            ->count();

        $returningCustomerRate = ($totalCustomers > 0)
            ? ($returningCustomersCount / $totalCustomers) * 100
            : 0;

        $averageItemsPerOrder = $completedOrders > 0
            ? ((clone $orderDetailQuery)->whereHas('order', fn($query) => $query->where('status', OrderStatus::Completed))->sum('qty') / $completedOrders)
            : 0;

        $totalDiscountGiven = (clone $orderQuery)->where('status', OrderStatus::Completed)
            ->sum('discount_value');

        // Order status labels + counts
        $orderStatusLabels = collect(OrderStatus::cases())->map(fn($s) => $s->label())->toArray();
        $orderStatusCounts = [
            (clone $orderQuery)->where('status', OrderStatus::Pending)->count(),
            (clone $orderQuery)->where('status', OrderStatus::Confirmed)->count(),
            (clone $orderQuery)->where('status', OrderStatus::Delivering)->count(),
            (clone $orderQuery)->where('status', OrderStatus::Completed)->count(),
            (clone $orderQuery)->where('status', OrderStatus::Cancelled)->count(),
        ];

        // Reviews average (global)
        $avgRating = Review::when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->avg('rating');

        // Product metrics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', DefaultActiveStatus::Active->value)->count();

        $inStockProducts = 0;
        $outOfStockProducts = 0;

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'total_revenue' => $totalRevenue,
                'new_customers' => $newCustomers,
                'total_customers' => $totalCustomers,
                'total_products_sold' => $totalProductsSold,
                'cancel_rate' => $cancelRate,
                'average_order_value' => $averageOrderValue,
                'months' => $months,
                'monthly_revenue' => $monthlyRevenue,
                'total_discount_given' => $totalDiscountGiven,
                'average_items_per_order' => $averageItemsPerOrder,
                'returning_customer_rate' => $returningCustomerRate,
                'new_customers_this_year' => $newCustomersThisYear,
                'order_status_labels' => $orderStatusLabels,
                'order_status_counts' => $orderStatusCounts,
                'avg_rating' => $avgRating,
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'in_stock_products' => $inStockProducts,
                'out_of_stock_products' => $outOfStockProducts,
            ],
        ]);
    }
}
