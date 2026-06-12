<?php

namespace App\Admin\Http\Controllers\Section;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Section\SectionRequest;
use App\Admin\Repositories\Section\SectionRepositoryInterface;
use App\Admin\Services\Section\SectionServiceInterface;
use App\Admin\DataTables\Section\SectionDataTable;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SectionController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        SectionRepositoryInterface $repository,
        SectionServiceInterface $service,
        CategoryRepositoryInterface $categoryRepository,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
        $this->categoryRepository = $categoryRepository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.sections.index',
            'create' => 'admin.sections.create',
            'edit' => 'admin.sections.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.section.index',
            'create' => 'admin.section.create',
            'edit' => 'admin.section.edit',
            'delete' => 'admin.section.delete'
        ];
    }

    public function index(SectionDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách section trang chủ'))
        ]);
    }

    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách section trang chủ'), route($this->route['index']))->add(__('add')),
            [
                'categories' => $this->categoryRepository->getFlatTree(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách section trang chủ'), route($this->route['index']))->add(__('edit')),
            [
                'categories' => $this->categoryRepository->getFlatTree(),
                'section' => $this->repository->findOrFailWithRelations($id),
            ]
        );
    }

    public function store(SectionRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }


    public function update(SectionRequest $request): RedirectResponse
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
