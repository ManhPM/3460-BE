<?php

namespace App\Api\AdminV1\Http\Controllers\AttributeVariation;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\AttributeVariation\AttributeVariationRequest;
use App\Api\AdminV1\Http\Resources\AttributeVariation\AttributeVariationResource;
use App\Admin\Repositories\AttributeVariation\AttributeVariationRepositoryInterface;
use App\Admin\Services\AttributeVariation\AttributeVariationServiceInterface;

class AttributeVariationController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        AttributeVariationRepositoryInterface $repository,
        AttributeVariationServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(int $attributeId)
    {
        $variations = $this->repository->getQueryBuilderByColumn('attribute_id', $attributeId)->get();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => AttributeVariationResource::collection($variations),
        ]);
    }

    public function show(int $id)
    {
        $variation = $this->repository->findOrFail($id);
        $variation->load('attribute');
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AttributeVariationResource($variation)
        ]);
    }

    public function store(AttributeVariationRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $variation = $this->service->store($request);
                return new AttributeVariationResource($variation->load('attribute'));
            },
            __('attribute_variation.created_success'),
            201
        );
    }

    public function update(AttributeVariationRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $request->merge(['id' => $id]);
                $variation = $this->service->update($request);
                return new AttributeVariationResource($variation->load('attribute'));
            },
            __('attribute_variation.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('attribute_variation.deleted_success')
        );
    }
}
