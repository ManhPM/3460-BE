<?php

namespace App\Api\AdminV1\Http\Controllers\Post;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Post\PostRequest;
use App\Api\AdminV1\Http\Resources\Post\PostResource;
use App\Api\AdminV1\Http\Resources\Post\PostCollection;
use App\Api\AdminV1\Repositories\Post\PostRepositoryInterface;
use App\Api\AdminV1\Services\Post\PostService;

class PostController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        PostRepositoryInterface $repository,
        PostService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $posts = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new PostCollection($posts),
        ]);
    }

    public function show(int $id)
    {
        $post = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new PostResource($post->load('categories'))
        ]);
    }

    public function store(PostRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $post = $this->service->create($request->validated());
                return new PostResource($post);
            },
            __('post.created_success'),
            201
        );
    }

    public function update(PostRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $post = $this->service->update($id, $request->validated());
                return new PostResource($post);
            },
            __('post.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('post.deleted_success')
        );
    }
}
