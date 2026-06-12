<?php

namespace App\Admin\Http\Controllers\Dashboard;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Enums\DefaultActiveStatus;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use App\Models\Admin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use AuthService;
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }


    public function getView()
    {
        return [
            'index' => 'admin.dashboard.index',
            'index-default' => 'admin.dashboard.index-default'
        ];
    }
    public function index()
    {
        $fromDate = request('from_date');
        $toDate = request('to_date');
        $adminId = request('admin_id'); // Cho SuperAdmin chọn chi nhánh

        $orderQuery = Order::query();
        $userQuery = User::query();
        $orderDetailQuery = OrderDetail::query();

        // Lấy admin hiện tại
        $currentAdmin = $this->getCurrentAdmin();
        $isSuperAdmin = $currentAdmin && $currentAdmin->hasRole('superAdmin');

        // Filter theo admin_id: branch admin chỉ thấy đơn hàng của mình, superAdmin có thể chọn chi nhánh
        if (!$isSuperAdmin && $currentAdmin) {
            // Branch admin: chỉ lấy đơn hàng của chi nhánh mình
            $orderQuery->where('admin_id', $currentAdmin->id);
            $orderDetailQuery->whereHas('order', fn($q) => $q->where('admin_id', $currentAdmin->id));
        } elseif ($isSuperAdmin && $adminId) {
            // SuperAdmin chọn chi nhánh cụ thể
            $orderQuery->where('admin_id', $adminId);
            $orderDetailQuery->whereHas('order', fn($q) => $q->where('admin_id', $adminId));
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

        // Lấy danh sách đơn hàng chưa xử lý để hiển thị warning
        $pendingOrdersList = (clone $orderQuery)
            ->where('status', OrderStatus::Pending)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'code', 'fullname', 'total', 'created_at']);
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

        // Lấy danh sách chi nhánh cho SuperAdmin
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Admin::whereHas('roles', function ($q) {
                $q->where('name', 'branch');
            })->get(['id', 'fullname', 'branch_name']);

            // Default chọn chi nhánh đầu tiên nếu superAdmin chưa chọn chi nhánh cụ thể
            if (!$adminId && $branches->count() > 0) {
                $adminId = $branches->first()->id;
            }
        }

        return view($this->view['index'], compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'newCustomers',
            'totalCustomers',
            'totalProductsSold',
            'cancelRate',
            'averageOrderValue',
            'months',
            'monthlyRevenue',
            'totalDiscountGiven',
            'averageItemsPerOrder',
            'returningCustomerRate',
            'newCustomersThisYear',
            // extras
            'orderStatusLabels',
            'orderStatusCounts',
            'avgRating',
            'totalProducts',
            'activeProducts',
            'fromDate',
            'toDate',
            'inStockProducts',
            'outOfStockProducts',
            // pending orders warning
            'pendingOrdersList',
            'isSuperAdmin',
            'branches',
            'adminId',
        ));
    }

    /**
     * API endpoint để lấy đơn hàng chưa xử lý theo admin_id
     */
    public function getPendingOrders()
    {
        $adminId = request('admin_id');
        $currentAdmin = $this->getCurrentAdmin();
        $isSuperAdmin = $currentAdmin && $currentAdmin->hasRole('superAdmin');

        $query = Order::where('status', OrderStatus::Pending);

        // Branch admin chỉ thấy đơn hàng của mình
        if (!$isSuperAdmin && $currentAdmin) {
            $query->where('admin_id', $currentAdmin->id);
        } elseif ($isSuperAdmin && $adminId) {
            // SuperAdmin có thể chọn chi nhánh
            $query->where('admin_id', $adminId);
        } elseif ($isSuperAdmin && !$adminId) {
            // SuperAdmin không chọn chi nhánh thì lấy tất cả
            // Không filter
        } else {
            // Không có quyền
            return response()->json([
                'status' => 403,
                'message' => 'Không có quyền truy cập',
                'data' => []
            ], 403);
        }

        $pendingOrders = $query->orderBy('created_at', 'desc')
            ->get(['id', 'code', 'fullname', 'total', 'created_at', 'admin_id']);

        return response()->json([
            'status' => 200,
            'message' => 'Thành công',
            'data' => $pendingOrders,
            'count' => $pendingOrders->count()
        ]);
    }
}
