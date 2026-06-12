<?php

namespace App\Api\AdminV1\Http\Controllers\Slider;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Slider\SliderItemRequest;
use App\Admin\Repositories\Slider\SliderItemRepositoryInterface;
use App\Admin\Services\Slider\SliderItemServiceInterface;

class SliderItemController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        SliderItemRepositoryInterface $repository,
        SliderItemServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(int $sliderId)
    {
        $items = $this->repository->getBy(['slider_id' => $sliderId]);

        $items = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'slider_id' => $item->slider_id,
                'title' => $item->title,
                'image' => $item->image ? asset($item->image) : null,
                'avatar' => $item->avatar ? asset($item->avatar) : null,
                'mobile_avatar' => $item->mobile_avatar ? asset($item->mobile_avatar) : null,
                'link' => $item->link,
                'position' => $item->position,
            ];
        });

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $items,
        ]);
    }

    public function show(int $id)
    {
        $item = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $item->load('slider')
        ]);
    }

    public function store(SliderItemRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $item = $this->service->store($request);
                return $item->load('slider');
            },
            __('slider_item.created_success'),
            201
        );
    }

    public function update(SliderItemRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $request->merge(['id' => $id]);
                $item = $this->service->update($request);
                return $item->load('slider');
            },
            __('slider_item.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('slider_item.deleted_success')
        );
    }
}
