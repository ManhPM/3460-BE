<?php

namespace App\Api\AdminV1\Http\Controllers\Slider;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Slider\SliderRequest;
use App\Api\AdminV1\Http\Resources\Slider\SliderResource;
use App\Api\AdminV1\Http\Resources\Slider\SliderCollection;
use App\Api\AdminV1\Repositories\Slider\SliderRepositoryInterface;
use App\Api\AdminV1\Services\Slider\SliderService;

class SliderController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        SliderRepositoryInterface $repository,
        SliderService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $sliders = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new SliderCollection($sliders),
        ]);
    }

    public function show(int $id)
    {
        $slider = $this->repository->findOrFailWithRelations($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new SliderResource($slider)
        ]);
    }

    public function store(SliderRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $slider = $this->service->create($request->validated());
                return new SliderResource($slider->load('items'));
            },
            __('slider.created_success'),
            201
        );
    }

    public function update(SliderRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $slider = $this->service->update($id, $request->validated());
                return new SliderResource($this->repository->findOrFailWithRelations($id));
            },
            __('slider.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('slider.deleted_success')
        );
    }
}
