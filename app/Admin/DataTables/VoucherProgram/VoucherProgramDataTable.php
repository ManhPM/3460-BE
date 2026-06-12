<?php

namespace App\Admin\DataTables\VoucherProgram;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\VoucherProgram\VoucherProgramRepositoryInterface;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Database\Eloquent\Builder;

class VoucherProgramDataTable extends BaseDataTable
{

    protected $nameTable = 'voucherProgramTable';

    public function __construct(
        VoucherProgramRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.voucher_programs.datatable.action',
            'type' => 'admin.voucher_programs.datatable.type',
            'discount' => 'admin.voucher_programs.datatable.discount',
            'voucher_type' => 'admin.voucher_programs.datatable.voucher_type',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6];

        $this->columnSearchDate = [];

        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => VoucherType::asSelectArray()
            ],
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
        return $this->repository->getByQueryBuilder([]);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.voucher_program', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'type' => $this->view['type'],
            'expiration_days' => '{{ $expiration_days }} {{ __("ngày") }}',
            'max_discount_value' => '{{ format_price($max_discount_value) }}',
            'min_order_amount' => '{{ format_price($min_order_amount) }}',
            'discount_value' => $this->view['discount'],
            'voucher_type' => $this->view['voucher_type'],
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
        $this->customRawColumns = ['action', 'type', 'discount_value', 'voucher_type'];
    }

    public function setCustomFilterColumns(): void {}
}
