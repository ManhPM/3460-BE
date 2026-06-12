<?php

namespace App\Admin\Http\Controllers\Post;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Post\PostRequest;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Admin\Services\Post\PostServiceInterface;
use App\Admin\DataTables\Post\PostDataTable;
use App\Enums\Post\PostStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\PostCategory;

class PostController extends Controller
{
    protected PostCategoryRepositoryInterface $repositoryPostCategory;
    protected PostCategory $modelCategory;

    public function __construct(
        PostRepositoryInterface $repository,
        PostCategoryRepositoryInterface $repositoryPostCategory,
        PostServiceInterface $service,
        PostCategory $modelCategory,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->repositoryPostCategory = $repositoryPostCategory;
        $this->service = $service;
        $this->modelCategory = $modelCategory;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.posts.index',
            'create' => 'admin.posts.create',
            'edit' => 'admin.posts.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.post.index',
            'create' => 'admin.post.create',
            'edit' => 'admin.post.edit',
            'delete' => 'admin.post.delete'
        ];
    }

    public function index(PostDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => PostStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh sách bài viết'))
        ]);
    }

    public function create(): Factory|View|Application
    {
        $categories = $this->modelCategory->scopePublished(
            $this->modelCategory->query()
        )->get();

        return view($this->view['create'], [
            'categories' => $categories,
            'status' => PostStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('Danh sách bài viết'),
                route($this->route['index'])
            )->add(__('add')),
        ]);
    }

    public function edit($id): Factory|View|Application
    {
        $categories = $this->modelCategory->scopePublished(
            $this->modelCategory->query()
        )->get();

        $post = $this->repository->findOrFailWithRelations($id);

        return view($this->view['edit'], [
            'categories' => $categories,
            'post' => $post,
            'status' => PostStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('Danh sách bài viết'),
                route($this->route['index'])
            )->add(__('edit'))
        ]);
    }



    public function store(PostRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(PostRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
