<?php

namespace App\Api\AdminV1\Http\Controllers\PostCategory;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\PostCategory\PostCategoryRequest;
use App\Api\AdminV1\Http\Resources\PostCategory\PostCategoryResource;
use App\Api\AdminV1\Http\Resources\PostCategory\PostCategoryCollection;
use App\Api\AdminV1\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Api\AdminV1\Services\PostCategory\PostCategoryService;

class PostCategoryController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        PostCategoryRepositoryInterface $repository,
        PostCategoryService $service
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
            'data' => new PostCategoryCollection($categories),
        ]);
    }

    public function show(int $id)
    {
        $category = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new PostCategoryResource($category->load(['parent', 'children']))
        ]);
    }

    public function store(PostCategoryRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $category = $this->service->create($request->validated());
                return new PostCategoryResource($category->load(['parent', 'children']));
            },
            __('post_category.created_success'),
            201
        );
    }

    public function update(PostCategoryRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $category = $this->service->update($id, $request->validated());
                return new PostCategoryResource($category->load(['parent', 'children']));
            },
            __('post_category.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('post_category.deleted_success')
        );
    }
}
