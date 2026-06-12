<?php

namespace App\Admin\Http\Controllers\Module;

use App\Admin\DataTables\Module\ModuleData1Table;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Module\ModuleRequest;
use App\Admin\Repositories\Module\ModuleRepositoryInterface;
use App\Admin\Services\Module\ModuleServiceInterface;
use App\Admin\DataTables\Module\ModuleDataTable;
use App\Enums\Module\ModuleStatus;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ModuleController extends Controller
{
    public function __construct(
        ModuleRepositoryInterface $repository,
        ModuleServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.module.index',
            'summary' => 'admin.module.summary',
            'create' => 'admin.module.create',
            'edit' => 'admin.module.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.module.index',
            'summary' => 'admin.module.summary',
            'create' => 'admin.module.create',
            'edit' => 'admin.module.edit',
            'delete' => 'admin.module.delete'
        ];
    }

    public function index(ModuleDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'actionMultiple' => [
                'delete' => "Xoá mục đã chọn"
            ],
            'breadcrumbs' => $this->crums->add(__('Danh sách Module'))
        ]);
    }


    public function summary(): Factory|View|Application
    {
        $listmodules = $this->repository->getAllModulesWithPermissions();
        return view($this->view['summary'], [
            'listmodules' => $listmodules
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách Module'), route($this->route['index']))->add(__('Thêm')),
            [
                'listpermissions' => $this->repository->getAllPermissions(),
                'status' => ModuleStatus::asSelectArray(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách Module'), route($this->route['index']))->add(__('Sửa')),
            [
                'module' => $this->repository->findOrFail($id),
                'listpermissions' => $this->repository->getAllPermissionsOfTheModule($id),
                'status' => ModuleStatus::asSelectArray(),
            ]
        );
    }


    public function store(ModuleRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(ModuleRequest $request): RedirectResponse
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

    public function actionMultipleRecord(Request $request)
    {
        $boolean = $this->service->actionMultipleRecord($request);
        if ($boolean) {
            return back()->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }
}
