<?php

namespace App\Admin\DataTables\Module;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Module\ModuleRepositoryInterface;
use App\Enums\Area\AreaStatus;
use App\Enums\Module\ModuleStatus;


class ModuleDataTable extends BaseDataTable
{


    protected $nameTable = 'moduleTable';


    public function __construct(
        ModuleRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.module.datatable.action',
            'name' => 'admin.module.datatable.name',
            'status' => 'admin.module.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => ModuleStatus::asSelectArray()
            ],

        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.module', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
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
        $this->customRawColumns = ['name', 'action', 'status', 'checkbox'];
    }
}
