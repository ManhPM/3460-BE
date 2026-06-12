<?php

namespace App\Api\AdminV1\Http\Controllers\Permission;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Permission\PermissionRequest;
use App\Api\AdminV1\Http\Resources\Permission\PermissionResource;
use App\Api\AdminV1\Repositories\Permission\PermissionRepositoryInterface;
use App\Admin\Services\Permission\PermissionServiceInterface;

class PermissionController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        PermissionRepositoryInterface $repository,
        PermissionServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $permissions = $this->repository->all();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => PermissionResource::collection($permissions)
        ]);
    }

    public function show(int $id)
    {
        $permission = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new PermissionResource($permission->load('module'))
        ]);
    }

    public function store(PermissionRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $permission = $this->service->store($request);
                return new PermissionResource($permission->load('module'));
            },
            __('permission.created_success'),
            201
        );
    }

    public function update(PermissionRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $request->merge(['id' => $id]);
                $permission = $this->service->update($request);
                return new PermissionResource($permission->load('module'));
            },
            __('permission.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('permission.deleted_success')
        );
    }
}
