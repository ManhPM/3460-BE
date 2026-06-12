<?php

namespace App\Admin\Http\Controllers\Slider;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Slider\SliderRequest;
use App\Admin\Repositories\Slider\SliderRepositoryInterface;
use App\Admin\Services\Slider\SliderServiceInterface;
use App\Admin\DataTables\Slider\SliderDataTable;
use App\Enums\Slider\SliderStatus;

class SliderController extends Controller
{
    public function __construct(
        SliderRepositoryInterface $repository,
        SliderServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;
    }

    public function getView()
    {
        return [
            'index' => 'admin.sliders.index',
            'create' => 'admin.sliders.create',
            'edit' => 'admin.sliders.edit'
        ];
    }

    public function getRoute()
    {
        return [
            'index' => 'admin.slider.index',
            'create' => 'admin.slider.create',
            'edit' => 'admin.slider.edit',
            'delete' => 'admin.slider.delete'
        ];
    }
    public function index(SliderDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => SliderStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('Danh sách slider'))
            ]
        );
    }
    public function create()
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách slider'), route($this->route['index']))->add(__('add')),
            [
                'status' => SliderStatus::asSelectArray(),
            ]
        );
    }

    public function edit($id)
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách slider'), route($this->route['index']))->add(__('edit')),
            [
                'slider' => $this->repository->findOrFail($id),
                'status' => SliderStatus::asSelectArray(),
            ]
        );
    }

    public function store(SliderRequest $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(SliderRequest $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id)
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
