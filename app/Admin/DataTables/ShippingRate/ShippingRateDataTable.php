<?php

namespace App\Admin\DataTables\ShippingRate;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\ShippingRate\ShippingRateRepositoryInterface;
use App\Enums\Discount\DiscountValueType;
use App\Enums\ShippingRate\ShippingRateType;
use Illuminate\Database\Eloquent\Builder;

class ShippingRateDataTable extends BaseDataTable
{

    protected $nameTable = 'shippingRateTable';

    public function __construct(
        ShippingRateRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.shipping_rates.datatable.action',
            'province' => 'admin.shipping_rates.datatable.province',
            'ward' => 'admin.shipping_rates.datatable.ward',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder([], ['province', 'ward']);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.shipping_rate', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'province' => $this->view['province'],
            'ward' => $this->view['ward'],
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
        $this->customRawColumns = ['action', 'province', 'ward'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'province' => function ($query, $keyword) {
                $query->whereHas('province', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },
            'ward' => function ($query, $keyword) {
                $query->whereHas('ward', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }
}
