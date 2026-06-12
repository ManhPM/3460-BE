<?php

namespace App\Admin\DataTables\MembershipLevel;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Admin\Traits\Roles;
use Illuminate\Database\Eloquent\Builder;

class MembershipLevelDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'userTable';

    public function __construct(
        MembershipLevelRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.membership_levels.datatable.action',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1];

        $this->columnSearchDate = [];

        $this->columnSearchSelect = [];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder([], []);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.membership_level', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [];
    }
}
