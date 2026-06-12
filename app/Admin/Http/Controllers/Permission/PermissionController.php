<?php

namespace App\Admin\Http\Controllers\Permission;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Permission\PermissionRequest;
use App\Admin\Repositories\Permission\PermissionRepositoryInterface;
use App\Admin\Services\Permission\PermissionServiceInterface;
use App\Admin\DataTables\Permission\PermissionDataTable;
use App\Enums\Permission\PermissionType;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        PermissionRepositoryInterface $repository,
        PermissionServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;
    }

    public function getView()
    {
        return [
            'index' => 'admin.permission.index',
            'create' => 'admin.permission.create',
            'edit' => 'admin.permission.edit'
        ];
    }

    public function getRoute()
    {
        return [
            'index' => 'admin.permission.index',
            'create' => 'admin.permission.create',
            'edit' => 'admin.permission.edit',
            'delete' => 'admin.permission.delete'
        ];
    }
    public function index(PermissionDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'actionMultiple' => [
                'delete' => 'Xoá mục đã chọn',
            ],
            'breadcrumbs' => $this->crums->add(__('Danh sách Quyền'))
        ]);
    }

    public function create()
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách Quyền'), route($this->route['index']))->add(__('Thêm')),
            [
                'listmodules' => $this->repository->getAllModules(),
                'types' => PermissionType::asSelectArray(),
            ]
        );
    }

    public function edit($id)
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách Quyền'), route($this->route['index']))->add(__('Sửa')),
            [
                'listmodules' => $this->repository->getAllModules(),
                'permission' => $this->repository->findOrFail($id),
                'types' => PermissionType::asSelectArray(),
            ]
        );
    }


    public function store(PermissionRequest $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(PermissionRequest $request)
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

    public function actionMultipleRecord(Request $request)
    {
        $boolean = $this->service->actionMultipleRecord($request);
        if ($boolean) {
            return back()->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }
}
