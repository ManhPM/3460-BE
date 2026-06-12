<?php

namespace App\Admin\Http\Controllers\Attribute;

use App\Admin\DataTables\Attribute\AttributeDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Attribute\AttributeRequest;
use App\Admin\Repositories\Attribute\AttributeRepositoryInterface;
use App\Admin\Services\Attribute\AttributeServiceInterface;
use App\Enums\Attribute\AttributeType;

class AttributeController extends Controller
{

    public function __construct(
        AttributeRepositoryInterface $repository,
        AttributeServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.attributes.index',
            'create' => 'admin.attributes.create',
            'edit' => 'admin.attributes.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.attribute.index',
            'create' => 'admin.attribute.create',
            'edit' => 'admin.attribute.edit',
            'delete' => 'admin.attribute.delete'
        ];
    }
    public function index(AttributeDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'type' => AttributeType::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('Danh sách thuộc tính'))
            ]
        );
    }

    public function create()
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách thuộc tính'), route($this->route['index']))->add(__('add')),
            ['type' => AttributeType::asSelectArray()]
        );
    }

    public function edit($id)
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách thuộc tính'), route($this->route['index']))->add(__('edit')),
            [
                'attribute' => $this->repository->findOrFail($id),
                'type' => AttributeType::asSelectArray()
            ]
        );
    }


    public function store(AttributeRequest $request)
    {

        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index']);
    }

    public function update(AttributeRequest $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id)
    {
        $instance = $this->repository->findOrFail($id);
        if (isset($instance->variations[0])) {
            return back()->with('error', __('Không thể xóa thuộc tính đang có sản phẩm.'));
        }
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
