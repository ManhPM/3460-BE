<?php

namespace App\Admin\DataTables\PostCategory;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Enums\DefaultStatus;
use App\Models\PostCategory;


class PostCategoryDataTable extends BaseDataTable
{
    protected $nameTable = 'postCategoryTable';

    public function __construct(
        PostCategoryRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.posts_categories.datatable.action',
            'name' => 'admin.posts_categories.datatable.editlink',
            'status' => 'admin.posts_categories.datatable.status',
            'parents_name' => 'admin.posts_categories.datatable.parents',
            'avatar' => 'admin.posts_categories.datatable.avatar',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3];

        $this->columnSearchDate = [3];

        $this->columnSearchSelect = [

            [
                'column' => 2,
                'data' => DefaultStatus::asSelectArray()
            ],
        ];
    }

    public function query()
    {
        $ids = $this->repository->getFlatTree()->pluck('id');
        return PostCategory::whereIn('id', $ids);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.post_category', ['categories']);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => $this->view['name'],
            'created_at' => '{{ format_date($created_at) }}',
            'status' => $this->view['status'],
            'parents_name' => function ($post_category) {
                return view($this->view['parents_name'], [
                    'parents_name' => $post_category->categories,
                ])->render();
            },
            'avatar' => $this->view['avatar'],
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
        $this->customRawColumns = ['name', 'action', 'status', 'parent_name', 'avatar'];
    }
}
