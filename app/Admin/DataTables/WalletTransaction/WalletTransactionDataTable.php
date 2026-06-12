<?php

namespace App\Admin\DataTables\WalletTransaction;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;

class WalletTransactionDataTable extends BaseDataTable
{
    protected $nameTable = 'walletTransactionTable';

    public function __construct(
        WalletTransactionRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.wallet_transaction.datatable.action',
            'user' => 'admin.wallet_transaction.datatable.user',
            'status' => 'admin.wallet_transaction.datatable.status',
            'type' => 'admin.wallet_transaction.datatable.type',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4];

        $this->columnSearchDate = [4];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => WalletTransactionType::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => WalletTransactionStatus::asSelectArray()
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getByQueryBuilder([], ['user']);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.wallet_transaction', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'user' => $this->view['user'],
            'amount' => '{{ format_price($amount) }}',
            'type' => $this->view['type'],
            'status' => $this->view['status'],
            'created_at' => '{{ format_datetime($created_at) }}',
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'user', 'status', 'type'];
    }
}
