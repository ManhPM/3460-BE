<?php

namespace App\Admin\Http\Controllers\Role;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Role\RoleRequest;
use App\Admin\Repositories\Role\RoleRepositoryInterface;
use App\Admin\Services\Role\RoleServiceInterface;
use App\Admin\DataTables\Role\RoleDataTable;

class RoleController extends Controller
{
    public function __construct(
        RoleRepositoryInterface $repository,
        RoleServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;
    }

    public function getView()
    {
        return [
            'index' => 'admin.role.index',
            'create' => 'admin.role.create',
            'edit' => 'admin.role.edit'
        ];
    }

    public function getRoute()
    {
        return [
            'index' => 'admin.role.index',
            'create' => 'admin.role.create',
            'edit' => 'admin.role.edit',
            'delete' => 'admin.role.delete'
        ];
    }
    public function index(RoleDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách vai trò'))
        ]);
    }

    public function create()
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách vai trò'), route($this->route['index']))->add(__('add')),
            [
                'listpermissions' => $this->repository->getAllPermissionsNoModule(),
                'listPermissionsInAllModules' => $this->repository->getAllPermissionsInAllModules(),
            ]
        );
    }

    public function edit($id)
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách vai trò'), route($this->route['index']))->add(__('edit')),
            [
                'role' => $this->repository->findOrFail($id),
                'permissions' => $this->repository->getAllPermissionsNoModule(),
                'listPermissionsInAllModules' => $this->repository->getAllPermissionsInAllModules(),
            ]
        );
    }


    public function store(RoleRequest $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(RoleRequest $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id)
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
