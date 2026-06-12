<?php

namespace App\Admin\DataTables\Voucher;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Database\Eloquent\Builder;

class VoucherDataTable extends BaseDataTable
{

    protected $nameTable = 'voucherTable';

    public function __construct(
        VoucherRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.vouchers.datatable.action',
            'title' => 'admin.vouchers.datatable.title',
            'code' => 'admin.vouchers.datatable.code',
            'type' => 'admin.vouchers.datatable.type',
            'discount' => 'admin.vouchers.datatable.discount',
            'user' => 'admin.vouchers.datatable.user',
            'voucher_type' => 'admin.vouchers.datatable.voucher_type',
            'is_used' => 'admin.vouchers.datatable.is_used',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6, 7, 8];

        $this->columnSearchDate = [2, 3];

        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => [0 => 'Chưa sử dụng', 1 => 'Đã sử dụng']
            ],
            [
                'column' => 6,
                'data' => VoucherType::asSelectArray()
            ],
            [
                'column' => 7,
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
        return $this->repository->getByQueryBuilder([], ['user']);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.voucher', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'code' => $this->view['code'],
            'type' => $this->view['type'],
            'date_end' => '{{ format_datetime($date_end, "d-m-Y") }}',
            'created_at' => '{{ format_datetime($created_at, "d-m-Y") }}',
            'min_order_amount' => '{{ format_price($min_order_amount) }}',
            'discount_value' => $this->view['discount'],
            'user' => $this->view['user'],
            'voucher_type' => $this->view['voucher_type'],
            'is_used' => $this->view['is_used'],
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
        $this->customRawColumns = ['action', 'code', 'type', 'discount_value', 'user', 'voucher_type', 'is_used'];
    }

    public function setCustomFilterColumns(): void {}
}
