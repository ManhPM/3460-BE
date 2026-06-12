<?php

namespace App\Api\AdminV1\Http\Controllers\Category;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Category\ProductCategoryRequest;
use App\Api\AdminV1\Http\Resources\Category\CategoryResource;
use App\Api\AdminV1\Http\Resources\Category\CategoryCollection;
use App\Api\AdminV1\Repositories\Category\CategoryRepositoryInterface;
use App\Api\AdminV1\Services\Category\CategoryService;

class CategoryController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        CategoryRepositoryInterface $repository,
        CategoryService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $categories = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new CategoryCollection($categories),
        ]);
    }

    public function store(ProductCategoryRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $category = $this->service->create($request->validated());
                return new CategoryResource($category);
            },
            __('category.created_success'),
            201
        );
    }

    public function show(int $id)
    {
        $category = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new CategoryResource($category)
        ]);
    }

    public function update(ProductCategoryRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $category = $this->service->update($id, $request->validated());
                return new CategoryResource($category);
            },
            __('category.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('category.deleted_success')
        );
    }
}
