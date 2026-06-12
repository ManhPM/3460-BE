<?php

namespace App\Admin\DataTables\FlashSale;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class FlashSaleDataTable extends BaseDataTable
{

    protected $nameTable = 'flashSaleTable';

    public function __construct(
        FlashSaleRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.flash_sales.datatable.action',
            'edit_link' => 'admin.flash_sales.datatable.edit-link',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2];

        $this->columnSearchDate = [1,2];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilder();

    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.flash_sale', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => $this->view['edit_link'],
            'start_time' => '{{ format_datetime($start_time) }}',
            'end_time' => '{{ format_datetime($end_time) }}',
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
        $this->customRawColumns = ['action', 'name'];
    }

    public function setCustomFilterColumns(): void
    {

    }
}
