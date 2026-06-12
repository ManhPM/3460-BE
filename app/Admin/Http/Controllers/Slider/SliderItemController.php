<?php

namespace App\Admin\Http\Controllers\Slider;

use App\Admin\Http\Controllers\Controller;
use App\Admin\DataTables\Slider\SliderItemDataTable;
use App\Admin\Repositories\Slider\{SliderRepositoryInterface, SliderItemRepositoryInterface};
use App\Admin\Services\Slider\SliderItemServiceInterface;
use App\Admin\Http\Requests\Slider\SliderItemRequest;

class SliderItemController extends Controller
{
    protected $repositorySlider;
    public function __construct(
        SliderItemRepositoryInterface $repository,
        SliderRepositoryInterface $repositorySlider,
        SliderItemServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->repositorySlider = $repositorySlider;
        $this->service = $service;
    }
    public function getView()
    {
        return [
            'index' => 'admin.sliders.items.index',
            'create' => 'admin.sliders.items.create',
            'edit' => 'admin.sliders.items.edit'
        ];
    }

    public function getRoute()
    {
        return [
            'index' => 'admin.slider.item.index',
            'create' => 'admin.slider.item.create',
            'edit' => 'admin.slider.item.edit',
            'delete' => 'admin.slider.item.delete'
        ];
    }
    public function index($slider_id, SliderItemDataTable $dataTable)
    {
        $slider = $this->repositorySlider->findOrFail($slider_id);
        return $dataTable->with('slider', $slider)->render($this->view['index'], [
            'slider' => $slider,
            'breadcrumbs' => $this->crums->add(__('Danh sách slider item'))
        ]);
    }

    public function create($slider_id)
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách slider'), route($this->route['index'], ['slider_id' => $slider_id]))->add(__('add')),
            [
                'slider' => $this->repositorySlider->findOrFail($slider_id),
                'breadcrumbs' => $this->crums->add(__('Danh sách slider item'))->add(__('add'))
            ]
        );
    }

    public function edit($id)
    {
        $instance = $this->repository->findOrFail($id);
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách slider'), route($this->route['index'], ['slider_id' => $instance->slider_id]))->add(__('edit')),
            [
                'sliderItem' => $this->repository->findOrFailWithRelations($id),
                'breadcrumbs' => $this->crums->add(__('Danh sách slider item'))->add(__('edit'))
            ]
        );
    }

    public function store(SliderItemRequest $request)
    {
        $slider_id = $request->input('slider_id');
        return $this->handleStoreResponseWithCustomParam(
            $slider_id,
            'slider_id',
            $request,
            function ($request) {
                return $this->service->store($request);
            },
            $this->route['index'],
        );
    }
    public function update(SliderItemRequest $request)
    {
        $instance = $this->repository->findOrFail($request->input('id'));
        $slider_id = $instance->slider_id;
        return $this->handleUpdateResponseWithCustomParam(
            $slider_id,
            'slider_id',
            $request,
            function ($request) {
                return $this->service->update($request);
            },
            $this->route['index'],
        );
    }

    public function delete($slider_id, $id)
    {
        return $this->handleDeleteResponseWithCustomParam(
            $id,
            $slider_id,
            'slider_id',
            function ($id) {
                return $this->service->delete($id);
            },
            $this->route['index'],
        );
    }
}
