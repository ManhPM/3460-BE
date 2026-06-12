<?php

namespace App\Admin\Http\Controllers\AttributeVariation;

use App\Admin\DataTables\AttributeVariation\AttributeVariationDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\AttributeVariation\AttributeVariationRequest;
use App\Admin\Repositories\AttributeVariation\AttributeVariationRepositoryInterface;
use App\Admin\Repositories\Attribute\AttributeRepositoryInterface;
use App\Admin\Services\AttributeVariation\AttributeVariationServiceInterface;
use App\Enums\Attribute\AttributeType;

class AttributeVariationController extends Controller
{
    protected $repositoryAttribute;

    public function __construct(
        AttributeVariationRepositoryInterface $repository,
        AttributeRepositoryInterface $repositoryAttribute,
        AttributeVariationServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;
        $this->repositoryAttribute = $repositoryAttribute;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.variations.index',
            'create' => 'admin.variations.create',
            'edit' => 'admin.variations.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.attribute.variation.index',
            'create' => 'admin.attribute.variation.create',
            'edit' => 'admin.attribute.variation.edit',
            'delete' => 'admin.attribute.variation.delete'
        ];
    }
    public function index($attribute_id, AttributeVariationDataTable $dataTable)
    {
        $attribute = $this->repositoryAttribute->findOrFail($attribute_id);
        return $dataTable->with('attribute', $attribute)->render($this->view['index'], [
            'attribute' => $attribute,
            'breadcrumbs' => $this->crums->add(__('Danh sách thuộc tính'), route($this->route['index'], ['attribute_id' => $attribute_id]))->add(__('Danh sách biến thể thuộc tính')),
        ]);
    }

    public function create($attribute_id)
    {
        $instance = $this->repositoryAttribute->findOrFail($attribute_id);

        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách thuộc tính'), route($this->route['index'], ['attribute_id' => $attribute_id]))->add(__('add')),
            [
                'attribute' => $instance,
                'has_meta_value_color' => $instance->type == AttributeType::Color
            ]
        );
    }

    public function edit($id)
    {
        $instance = $this->repository->findOrFailWithRelations($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách thuộc tính'), route($this->route['index'], ['attribute_id' => $instance->attribute_id]))->add(__('edit')),
            [
                'variation' => $instance,
                'has_meta_value_color' => optional($instance->attribute)->type == AttributeType::Color
            ]
        );
    }

    public function store(AttributeVariationRequest $request)
    {
        $attribute_id = $request->input('attribute_id');
        return $this->handleStoreResponseWithCustomParam(
            $attribute_id,
            'attribute_id',
            $request,
            function ($request) {
                return $this->service->store($request);
            },
            $this->route['index'],
        );
    }

    public function update(AttributeVariationRequest $request)
    {

        $instance = $this->repository->findOrFail($request->input('id'));
        $attribute_id = $instance->attribute_id;
        return $this->handleUpdateResponseWithCustomParam(
            $attribute_id,
            'attribute_id',
            $request,
            function ($request) {
                return $this->service->update($request);
            },
            $this->route['index'],
        );
    }

    public function delete($attribute_id, $id)
    {
        return $this->handleDeleteResponseWithCustomParam(
            $id,
            $attribute_id,
            'attribute_id',
            function ($id) {
                return $this->service->delete($id);
            },
            $this->route['index'],
        );
    }
}
