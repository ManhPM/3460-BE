<?php

namespace App\Admin\DataTables\AttributeVariation;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\AttributeVariation\AttributeVariationRepositoryInterface;


class AttributeVariationDataTable extends BaseDataTable
{


    protected $nameTable = 'attributeVariationTable';


    public function __construct(
        AttributeVariationRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.variations.datatable.action',
            'editlink_color' => 'admin.variations.datatable.editlink-color',
            'editlink' => 'admin.variations.datatable.editlink'
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2];


    }

    public function query()
    {
        return $this->repository->getQueryBuilderByColumn('attribute_id', $this->attribute->id);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.attributes_variations', []);

    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => $this->view['editlink_color'],
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
        $this->customRawColumns = ['name', 'action'];
    }


}
