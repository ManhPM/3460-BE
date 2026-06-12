<?php

namespace App\Api\AdminV1\Http\Controllers\Role;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Role\RoleRequest;
use App\Api\AdminV1\Http\Resources\Role\RoleResource;
use App\Api\AdminV1\Http\Resources\Role\RoleCollection;
use App\Api\AdminV1\Repositories\Role\RoleRepositoryInterface;
use App\Api\AdminV1\Services\Role\RoleService;

class RoleController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        RoleRepositoryInterface $repository,
        RoleService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $roles = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new RoleCollection($roles),
        ]);
    }

    public function show(int $id)
    {
        $role = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new RoleResource($role->load('permissions'))
        ]);
    }

    public function store(RoleRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $role = $this->service->create($request->validated());
                return new RoleResource($role);
            },
            __('role.created_success'),
            201
        );
    }

    public function update(RoleRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $role = $this->service->update($id, $request->validated());
                return new RoleResource($role);
            },
            __('role.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('role.deleted_success')
        );
    }
}
