<?php

namespace App\Admin\Http\Controllers\PostCategory;

use App\Admin\DataTables\PostCategory\PostCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\PostCategory\PostCategoryRequest;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Admin\Services\PostCategory\PostCategoryServiceInterface;
use App\Enums\PostCategory\PostCategoryStatus;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PostCategoryController extends Controller
{
    public function __construct(
        PostCategoryRepositoryInterface $repository,
        PostCategoryServiceInterface    $service
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.posts_categories.index',
            'create' => 'admin.posts_categories.create',
            'edit' => 'admin.posts_categories.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.post_category.index',
            'create' => 'admin.post_category.create',
            'edit' => 'admin.post_category.edit',
            'delete' => 'admin.post_category.delete'
        ];
    }

    public function index(PostCategoryDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách chuyên mục bài viết'))
        ]);
    }



    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách chuyên mục bài viết'), route($this->route['index']))->add(__('add')),
            [
                'categories' => $this->repository->getFlatTree(),
                'status' => PostCategoryStatus::asSelectArray(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách chuyên mục bài viết'), route($this->route['index']))->add(__('edit')),
            [
                'category' => $this->repository->findOrFail($id),
                'categories' => $this->repository->getFlatTreeNotInNode([$id]),
                'status' => PostCategoryStatus::asSelectArray(),
            ]
        );
    }


    public function store(PostCategoryRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }


    public function update(PostCategoryRequest $request): RedirectResponse
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
