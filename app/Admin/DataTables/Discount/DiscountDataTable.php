<?php

namespace App\Admin\DataTables\Discount;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Enums\Discount\DiscountValueType;
use Illuminate\Database\Eloquent\Builder;

class DiscountDataTable extends BaseDataTable
{

    protected $nameTable = 'discountTable';

    public function __construct(
        DiscountRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.discounts.datatable.action',
            'title' => 'admin.discounts.datatable.title',
            'edit_link' => 'admin.discounts.datatable.edit-link',
            'type' => 'admin.discounts.datatable.type',
            'discount' => 'admin.discounts.datatable.discount',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6];

        $this->columnSearchDate = [1, 2];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => DiscountValueType::asSelectArray()
            ],
        ];
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
        $this->customColumns = config('datatables_columns.discount', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'code' => $this->view['edit_link'],
            'type' => $this->view['type'],
            'date_end' => '{{ format_datetime($date_end) }}',
            'date_start' => '{{ format_datetime($date_start) }}',
            'min_order_amount' => '{{ format_price($min_order_amount) }}',
            'discount_value' => $this->view['discount'],
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
        $this->customRawColumns = ['action', 'code', 'type', 'discount_value'];
    }

    public function setCustomFilterColumns(): void {}
}
