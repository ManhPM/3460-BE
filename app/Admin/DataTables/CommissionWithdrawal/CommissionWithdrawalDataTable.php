<?php

namespace App\Admin\DataTables\CommissionWithdrawal;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Enums\WithdrawStatus;
use Illuminate\Database\Eloquent\Builder;

class CommissionWithdrawalDataTable extends BaseDataTable
{

    protected $nameTable = 'commissionWithdrawalTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        CommissionWithdrawalRepositoryInterface $repository
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
                'column' => 3,
                'data' => WithdrawStatus::asSelectArray()
            ],
        ];
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.commission_withdrawals.datatable.action',
            'editlink' => 'admin.commission_withdrawals.datatable.editlink',
            'status' => 'admin.commission_withdrawals.datatable.status',
            'user' => 'admin.commission_withdrawals.datatable.user',
            'bank_account_number' => 'admin.commission_withdrawals.datatable.bank_account_number',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'id' => $this->view['editlink'],
            'status' => $this->view['status'],
            'user' => $this->view['user'],
            'bank_account_number' => $this->view['bank_account_number'],
            'created_at' => '{{ format_datetime($created_at) }}',
            'amount' => '{{ format_price($amount) }}',
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $query = $this->repository->getByQueryBuilder([], ['user']);
        if (auth('web')->user()) {
            $query->where('user_id', auth('web')->user()->id);
        }
        return $query;
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
        $config = config('datatables_columns.commission_withdrawal', []);
        if (!auth('admin')->user()) {
            unset($config['action']);
        }
        $this->customColumns = $config;
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
        $this->customRawColumns = ['id', 'status', 'bank_account_number', 'user', 'action'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', '%' . $keyword . '%');
                });
            },
            'bank_account_number' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('bank_account_number', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }
}
