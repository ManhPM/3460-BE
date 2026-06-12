<?php

namespace App\Admin\DataTables\Slider;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Slider\SliderItemRepositoryInterface;
use App\Admin\Traits\GetConfig;

class SliderItemDataTable extends BaseDataTable
{

    use GetConfig;
    /**
     * Available button actions. When calling an action, the value will be used
     * as the function name (so it should be available)
     * If you want to add or disable an action, overload and modify this property.
     *
     * @var array
     */
    // protected array $actions = ['pageLength', 'excel', 'reset', 'reload'];
    protected array $actions = ['reset', 'reload'];

    public function __construct(
        SliderItemRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }

    public function setView()
    {
        $this->view = [
            'action' => 'admin.sliders.items.datatable.action',
            'editlink' => 'admin.sliders.items.datatable.editlink',
            'avatar' => 'admin.sliders.items.datatable.avatar',
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->repository->getQueryBuilderByColumns('slider_id', $this->slider->id);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'title' => $this->view['editlink'],
            'created_at' => '{{ format_date($created_at) }}',
            'avatar' => $this->view['avatar'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function setCustomColumns()
    {
        $this->customColumns = $this->traitGetConfigDatatableColumns('slider_item');
    }

    protected function filename(): string
    {
        return 'sliderItem_' . date('YmdHis');
    }

    protected function editColumnCreatedAt()
    {
        $this->instanceDataTable = $this->instanceDataTable->editColumn('created_at', '{{ date("d-m-Y", strtotime($created_at)) }}');
    }
    protected function setCustomRawColumns()
    {
        $this->customRawColumns = ['title', 'avatar', 'action'];
    }


    protected function setColumnSearch()
    {
        // TODO: Implement setColumnSearch() method.
    }
}
