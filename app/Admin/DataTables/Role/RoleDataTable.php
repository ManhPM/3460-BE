<?php

namespace App\Admin\DataTables\Role;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Role\RoleRepositoryInterface;


class RoleDataTable extends BaseDataTable
{
    protected $nameTable = 'roleTable';


    protected array $actions = ['reset', 'reload', 'excel'];

    public function __construct(
        RoleRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }


    public function setView(): void
    {
        $this->view = [
            'id' => 'admin.role.datatable.cotid',
            'title' => 'admin.role.datatable.title',
            'guardname' => 'admin.role.datatable.guardname',
            'action' => 'admin.role.datatable.action',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2];


        $this->columnSearchSelect = [];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder();
    }


    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.role', []);
    }


    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'title' => $this->view['title'],
            'guard_name' => $this->view['guardname']
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
        $this->customRawColumns = ['title', 'action', 'guard_name'];
    }
}
