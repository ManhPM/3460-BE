<?php

namespace App\Admin\DataTables\Admin;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Admin\AdminRepositoryInterface;

class AdminDataTable extends BaseDataTable
{

    protected $nameTable = 'adminTable';

    public function __construct(
        AdminRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.admins.datatable.action',
            'roles' => 'admin.admins.datatable.roles',
            'edit-link' => 'admin.admins.datatable.editlink'

        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2, 3, 4];

        $this->columnSearchDate = [4];

    }

    public function query()
    {
        return $this->repository->getByQueryBuilder([], ['roles'])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'branch');
            })
            ->where('id', '!=', 1); // Ẩn superAdmin tổng
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.admin', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ format_date($created_at) }}',
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'roles' => function ($instance) {
                return $instance->roles->pluck('title')->join(', ');
            },
            'fullname' =>$this->view['edit-link']
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'roles','fullname'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'roles' => function ($query, $keyword) {
                $query->whereHas('roles', function ($subQuery) use ($keyword) {
                    $subQuery->where('title', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }
}
