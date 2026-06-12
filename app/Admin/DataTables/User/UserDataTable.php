<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Builder;

class UserDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'userTable';

    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.users.datatable.action',
            'fullname' => 'admin.users.datatable.fullname',
            'is_email_verified' => 'admin.users.datatable.is_email_verified',
            'is_phone_verified' => 'admin.users.datatable.is_phone_verified',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6];

        $this->columnSearchDate = [6];

        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => [0 => 'Chưa xác thực', 1 => 'Đã xác thực']
            ],
            [
                'column' => 4,
                'data' => [0 => 'Chưa xác thực', 1 => 'Đã xác thực']
            ],
            [
                'column' => 5,
                'data' => Gender::asSelectArray()
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
        return $this->repository->getQueryBuilderOrderBy()->whereHas('roles', function ($query) {
            $query->where('name', $this->getRoleCustomer());
        });
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.user', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'fullname' => $this->view['fullname'],
            'is_email_verified' => $this->view['is_email_verified'],
            'is_phone_verified' => $this->view['is_phone_verified'],
            'gender' => function ($user) {
                return $user->gender->description();
            },

            'created_at' => '{{ format_date($created_at) }}'
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
        $this->customRawColumns = ['fullname', 'action', 'is_email_verified', 'is_phone_verified'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'fullname' => function ($query, $keyword) {
                $query->where('fullname', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%');
            },
        ];
    }
}
