<?php

namespace App\Api\AdminV1\Http\Controllers\Module;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Module\ModuleRequest;
use App\Admin\Repositories\Module\ModuleRepositoryInterface;
use App\Admin\Services\Module\ModuleServiceInterface;

class ModuleController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        ModuleRepositoryInterface $repository,
        ModuleServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $modules = $this->repository->all();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $modules,
        ]);
    }

    public function show(int $id)
    {
        $module = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $module->load('permissions')
        ]);
    }

    public function store(ModuleRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $module = $this->service->store($request);
                return $module->load('permissions');
            },
            __('module.created_success'),
            201
        );
    }

    public function update(ModuleRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $request->merge(['id' => $id]);
                $module = $this->service->update($request);
                return $module->load('permissions');
            },
            __('module.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('module.deleted_success')
        );
    }
}
