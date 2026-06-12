<?php

namespace App\Admin\DataTables\Order;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;

class UserOrderDataTable extends BaseDataTable
{

    use AuthService;
    protected $nameTable = 'userOrderTable';

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
                'column' => 1,
                'data' => PaymentMethod::asSelectArray()
            ],
            [
                'column' => 2,
                'data' => PaymentStatus::asSelectArray()
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
            'action' => 'user.orders.datatable.action',
            'editlink' => 'user.orders.datatable.editlink',
            'status' => 'user.orders.datatable.status',
            'payment_status' => 'user.orders.datatable.payment_status',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'id' => $this->view['editlink'],
            'status' => $this->view['status'],
            'payment_status' => $this->view['payment_status'],
            'payment_method' => '{{ App\Enums\Payment\PaymentMethod::getDescription($payment_method) }}',
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
        $userId = $this->getCurrentUserId();
        return $this->repository->getByQueryBuilder(['user_id' => $userId], ['user']);
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
        $this->customColumns = config('datatables_columns.order_user', []);
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
        $this->customRawColumns = ['id', 'status', 'action', 'payment_status'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'id' => function ($query, $keyword) {
                $query->where('code', 'like', '%' . $keyword . '%');
            },
        ];
    }
}
