<?php

namespace App\Admin\DataTables\Bank;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Bank\BankRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class BankDataTable extends BaseDataTable
{

    protected $nameTable = 'bankTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        BankRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }
    protected function setColumnSearch()
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4, 5];


        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => [0 => 'Ngưng hoạt động', 1 => 'Đang hoạt động']
            ],
        ];
    }

    public function setView(): void
    {
        $this->view = [
            'is_active' => 'admin.banks.datatable.is_active',
            'logo' => 'admin.banks.datatable.logo',
            'name' => 'admin.banks.datatable.name',
            'action' => 'admin.banks.datatable.action',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'is_active' => $this->view['is_active'],
            'logo' => $this->view['logo'],
            'name' => $this->view['name'],
            'action' => $this->view['action'],
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilderOrderBy('is_active', 'desc');
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
        $this->customColumns = config('datatables_columns.bank', []);
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['id', 'is_active', 'logo', 'name', 'action'];
    }

    public function setCustomFilterColumns(): void {}
}
