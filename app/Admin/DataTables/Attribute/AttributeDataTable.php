<?php

namespace App\Admin\DataTables\Attribute;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Attribute\AttributeRepositoryInterface;
use App\Enums\Attribute\AttributeType;

class AttributeDataTable extends BaseDataTable
{


    protected $nameTable = 'attributeTable';


    public function __construct(
        AttributeRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.attributes.datatable.action',
            'editlink' => 'admin.attributes.datatable.editlink',
            'type' => 'admin.attributes.datatable.type',
            'variations' => 'admin.attributes.datatable.variations',
        ];
    }

    protected function setColumnSearch()
    {
        $this->columnAllSearch = [0, 1, 2];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => AttributeType::asSelectArray()
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderWithRelations();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.attribute', []);

    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ format_date($created_at) }}',
            'name' => $this->view['editlink'],
            'type' => $this->view['type'],
            'variations' => $this->view['variations'],
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
        $this->customRawColumns = ['name', 'action', 'variations', 'type'];
    }
}
