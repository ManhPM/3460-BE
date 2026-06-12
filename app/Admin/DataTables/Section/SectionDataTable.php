<?php

namespace App\Admin\DataTables\Section;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Section\SectionRepositoryInterface;
use App\Admin\Traits\GetConfig;

class SectionDataTable extends BaseDataTable
{

    use GetConfig;
    protected $nameTable = 'SectionTable';
    protected array $actions = ['reset', 'reload'];

    public function __construct(
        SectionRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.sections.datatable.action',
            'avatar' => 'admin.sections.datatable.avatar',
            'editlink' => 'admin.sections.datatable.editlink',
            'status' => 'admin.sections.datatable.status',
            'is_active' => 'admin.sections.datatable.is_active',
            'is_rightside' => 'admin.sections.datatable.is_rightside',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => [0 => 'Bên trái', 1 => 'Bên phải']
            ],
            [
                'column' => 3,
                'data' => [0 => 'Ngưng hoạt động', 1 => 'Đang hoạt động']
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy('position', 'asc');
    }


    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.section', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'avatar' => $this->view['avatar'],
            'status' => $this->view['status'],
            'title' => $this->view['editlink'],
            'is_rightside' => $this->view['is_rightside'],
            'is_active' => $this->view['is_active'],
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
        $this->customRawColumns = ['avatar', 'title', 'status', 'is_rightside', 'is_active', 'action'];
    }
}
