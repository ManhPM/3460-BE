<?php

namespace App\Admin\DataTables\Product;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Enums\Product\ProductType;

class ProductDataTable extends BaseDataTable
{
    protected $nameTable = 'productTable';
    protected CategoryRepositoryInterface $repoCat;
    protected $productIds;
    public function __construct(
        ProductRepositoryInterface $repository,
        CategoryRepositoryInterface $repoCat,
        $productIds = [],
    ) {
        $this->repository = $repository;
        $this->repoCat = $repoCat;
        parent::__construct();
        $this->productIds = $productIds;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.products.datatable.action',
            'avatar' => 'admin.products.datatable.avatar',
            'edit_link' => 'admin.products.datatable.editlink',
            'price' => 'admin.products.datatable.price',
            'categories' => 'admin.products.datatable.categories',
            'is_active' => 'admin.products.datatable.is_active',
            'type' => 'admin.products.datatable.type',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];

        $this->columnSearchDate = [5];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => ProductType::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => [0 => 'Ngưng hoạt động', 1 => 'Hoạt động']
            ],
        ];
    }

    public function query()
    {
        $query = $this->repository->getQueryBuilderWithRelations();

        if (!empty($this->productIds)) {
            $query->whereIn('id', $this->productIds);
        }

        return $query;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.product', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => $this->view['edit_link'],
            'avatar' => $this->view['avatar'],
            'is_active' => $this->view['is_active'],
            'categories' => $this->view['categories'],
            'created_at' => '{{ format_date($created_at) }}',
            'type' => $this->view['type'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'price' => $this->view['price'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'avatar', 'name', 'price', 'categories', 'is_active', 'type'];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'categories' => function ($query, $keyword) {
                $query->whereHas('categories', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },
            'price' => function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('price', 'like', '%' . $keyword . '%')
                        ->orWhere('promotion_price', 'like', '%' . $keyword . '%')
                        ->orWhereHas('productVariations', function ($subQuery) use ($keyword) {
                            $subQuery->where('price', 'like', '%' . $keyword . '%')
                                ->orWhere('promotion_price', 'like', '%' . $keyword . '%');
                        });
                });
            },
        ];
    }
}
