<?php

namespace App\Admin\DataTables\Notification;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Enums\Notification\NotificationStatus;
use Illuminate\Database\Eloquent\Builder;

class NotificationDataTable extends BaseDataTable
{

    protected $nameTable = 'notificationTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        NotificationRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }
    protected function setColumnSearch()
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];

        $this->columnSearchDate = [6];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => NotificationStatus::asSelectArray()
            ],
        ];
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.notifications.datatable.action',
            'status' => 'admin.notifications.datatable.status',
            'user' => 'admin.notifications.datatable.user',
            'id' => 'admin.notifications.datatable.id',
            'checkbox' => 'admin.notifications.datatable.checkbox',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'user' => $this->view['user'],
            'id' => $this->view['id'],
            'created_at' => '{{ format_date($created_at) }}',
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
        $this->customColumns = config('datatables_columns.notification', []);
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'checkbox' => $this->view['checkbox'],
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['id', 'status', 'user', 'action', 'checkbox'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }
}
