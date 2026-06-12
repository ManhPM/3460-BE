<?php

namespace App\Api\V1\Http\Controllers\PostCategory;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\PostCategory\PostCategoryRequest;
use App\Api\V1\Http\Resources\PostCategory\AllPostCategoryTreeResource;
use App\Api\V1\Http\Resources\PostCategory\ShowCategoryWithPostResource;
use App\Api\V1\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Api\V1\Repositories\Post\PostRepositoryInterface;
use \Illuminate\Http\Request;

/**
 * @group Chuyên mục
 */

class PostCategoryController extends Controller
{
    protected $repositoryProduct;
    protected $repositoryPost;
    public function __construct(
        PostCategoryRepositoryInterface $repository,
        PostRepositoryInterface $repositoryPost
    ) {
        $this->repository = $repository;
        $this->repositoryPost = $repositoryPost;
    }

    public function index()
    {

        $categories = $this->repository->getTree();
        $categories = new AllPostCategoryTreeResource($categories);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $categories
        ]);
    }
    public function show($id)
    {
        try {
            $category = $this->repository->findByIdWithAncestorsAndDescendants($id);
            $category = new ShowCategoryWithPostResource($category, $this->repositoryPost);

            return response()->json([
                'status' => 200,
                'message' => __('success'),
                'data' => $category
            ]);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'status' => 404,
                'message' => __('not_found_data')
            ], 404);
        }
    }
}
