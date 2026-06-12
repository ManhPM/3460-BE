<?php

namespace App\Admin\DataTables\Review;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Review\ReviewRepositoryInterface;
use App\Enums\Review\ReviewStatus;
use App\Enums\Payment\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;

class ReviewDataTable extends BaseDataTable
{

    protected $nameTable = 'reviewTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        ReviewRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }
    protected function setColumnSearch()
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4, 5];

        $this->columnSearchDate = [5];
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.reviews.datatable.action',
            'user' => 'admin.reviews.datatable.user',
            'rating' => 'admin.reviews.datatable.rating',
            'product' => 'admin.reviews.datatable.product',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'user' => $this->view['user'],
            'product' => $this->view['product'],
            'rating' => $this->view['rating'],
            'created_at' => '{{ format_datetime($created_at) }}',
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder([], ['user', 'product']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */

    /**
     * Get columns.
     *
     * @return void
     */
    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.review', []);
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function filename(): string
    {
        return 'review_' . date('YmdHis');
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['product', 'user', 'action', 'rating'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', '%' . $keyword . '%');
                });
            },
            'product' => function ($query, $keyword) {
                $query->whereHas('product', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }
}
