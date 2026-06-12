<?php

namespace App\Admin\DataTables\Slider;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Slider\SliderRepositoryInterface;
use App\Admin\Traits\GetConfig;

class SliderDataTable extends BaseDataTable
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
        SliderRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }

    public function setView()
    {
        $this->view = [
            'action' => 'admin.sliders.datatable.action',
            'editlink' => 'admin.sliders.datatable.editlink',
            'status' => 'admin.sliders.datatable.status',
            'items' => 'admin.sliders.datatable.items',
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
        return $this->repository->getQueryBuilderWithRelations();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.slider', []);
    }

    protected function setCustomEditColumns()
    {
        $this->customEditColumns = [
            'items' => $this->view['items'],
            'status' => $this->view['status'],
            'name' => $this->view['editlink'],
            'created_at' => '{{ date("d-m-Y", strtotime($created_at)) }}',
        ];
    }

    protected function setCustomAddColumns()
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function filename(): string
    {
        return 'slider_' . date('YmdHis');
    }

    protected function addColumnAction()
    {
        $this->instanceDataTable = $this->instanceDataTable->addColumn('action', $this->view['action']);
    }
    protected function setCustomRawColumns()
    {
        $this->customRawColumns = ['name', 'status', 'items', 'action'];
    }

    protected function setColumnSearch()
    {
        // TODO: Implement setColumnSearch() method.
    }
}
