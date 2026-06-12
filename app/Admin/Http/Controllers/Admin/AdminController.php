<?php

namespace App\Admin\Http\Controllers\Admin;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Admin\AdminRequest;
use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Services\Admin\AdminServiceInterface;
use App\Admin\DataTables\Admin\AdminDataTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function __construct(
        AdminRepositoryInterface $repository,
        AdminServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.admins.index',
            'create' => 'admin.admins.create',
            'edit' => 'admin.admins.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.admin.index',
            'create' => 'admin.admin.create',
            'edit' => 'admin.admin.edit',
            'delete' => 'admin.admin.delete'
        ];
    }
    public function index(AdminDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách admin'))
        ]);
    }

    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách admin'), route($this->route['index']))->add(__('add')),
            ['roles' => $this->repository->getAllRolesByGuardName('admin')]
        );
    }

    public function edit($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách admin'), route($this->route['index']))->add(__('edit')),
            [
                'admin' => $this->repository->findOrFail($id),
                'roles' => $this->repository->getAllRolesByGuardName('admin')
            ]
        );
    }


    public function store(AdminRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }


    public function update(AdminRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        $instance = $this->repository->findOrFail($id);
        if ($instance->id == 1) {
            return back()->with('error', __('Không thể xóa admin mặc định.'));
        }
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
