<?php

namespace App\Admin\DataTables\Order;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;

class OrderDataTable extends BaseDataTable
{

    protected $nameTable = 'orderTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        OrderRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }
    protected function setColumnSearch()
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4, 5];

        $this->columnSearchDate = [5];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => PaymentMethod::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => OrderStatus::asSelectArray()
            ],
        ];
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.orders.datatable.action',
            'admin' => 'admin.orders.datatable.admin',
            'editlink' => 'admin.orders.datatable.editlink',
            'status' => 'admin.orders.datatable.status',
            'user' => 'admin.orders.datatable.user',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'id' => $this->view['editlink'],
            'admin' => $this->view['admin'],
            'status' => $this->view['status'],
            'total' => '{{ format_price($total - $discount_value + $shipping_fee - $voucher_shipping_discount_value - $voucher_product_discount_value - $points_discount_value - $membership_discount_value) }}',
            'payment_method' => '{{ App\Enums\Payment\PaymentMethod::getDescription($payment_method) }}',
            'user' => $this->view['user'],
            'created_at' => '{{ format_datetime($created_at) }}',
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $builder = $this->repository->getByQueryBuilder([], ['user', 'admin'])->where('is_deleted', 0);

        // Restrict data to current admin for branch role
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            $builder->where('admin_id', $admin->id);
        }

        return $builder;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */

    /**
     * Get columns.
     *
     * @return void
     */
    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.order', []);
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function filename(): string
    {
        return 'order_' . date('YmdHis');
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['id', 'status', 'payment_status', 'user', 'admin', 'action'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', '%' . $keyword . '%');
                });
            },
            'id' => function ($query, $keyword) {
                $query->where('code', 'like', '%' . $keyword . '%');
            },
        ];
    }
}
