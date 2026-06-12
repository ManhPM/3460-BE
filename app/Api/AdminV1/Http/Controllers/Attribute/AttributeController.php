<?php

namespace App\Api\AdminV1\Http\Controllers\Attribute;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Attribute\AttributeRequest;
use App\Api\AdminV1\Http\Resources\Attribute\AttributeResource;
use App\Api\AdminV1\Http\Resources\Attribute\AttributeCollection;
use App\Api\AdminV1\Repositories\Attribute\AttributeRepositoryInterface;
use App\Api\AdminV1\Services\Attribute\AttributeService;

class AttributeController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        AttributeRepositoryInterface $repository,
        AttributeService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $attributes = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AttributeCollection($attributes),
        ]);
    }

    public function show(int $id)
    {
        $attribute = $this->repository->findOrFail($id);
        $attribute->load('variations');
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AttributeResource($attribute)
        ]);
    }

    public function store(AttributeRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $attribute = $this->service->create($request->validated());
                return new AttributeResource($attribute->load('variations'));
            },
            __('attribute.created_success'),
            201
        );
    }

    public function update(AttributeRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $attribute = $this->service->update($id, $request->validated());
                return new AttributeResource($attribute->load('variations'));
            },
            __('attribute.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                $instance = $this->repository->findOrFail($id);
                if (isset($instance->variations[0])) {
                    throw new \Exception(__('attribute.cannot_delete_has_products'));
                }
                return $this->service->delete($id);
            },
            __('attribute.deleted_success')
        );
    }
}
