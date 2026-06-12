<?php

namespace App\Api\AdminV1\Http\Controllers\Admin;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Admin\AdminRequest;
use App\Api\AdminV1\Http\Resources\Admin\AdminResource;
use App\Api\AdminV1\Http\Resources\Admin\AdminCollection;
use App\Api\AdminV1\Repositories\Admin\AdminRepositoryInterface;
use App\Api\AdminV1\Services\Admin\AdminService;

class AdminController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        AdminRepositoryInterface $repository,
        AdminService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $admins = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AdminCollection($admins),
        ]);
    }

    public function show(int $id)
    {
        $admin = $this->repository->findOrFailWithRelations($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AdminResource($admin)
        ]);
    }

    public function store(AdminRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $data = $request->validated();
                $roleId = $data['role_id'] ?? null;
                unset($data['role_id']);

                $admin = $this->service->create($data, $roleId);

                return new AdminResource($this->repository->findOrFailWithRelations($admin->id));
            },
            __('admin.created_success'),
            201
        );
    }

    public function update(AdminRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $data = $request->validated();
                $roleId = $data['role_id'] ?? null;
                unset($data['role_id']);

                $admin = $this->service->update($id, $data, $roleId);

                return new AdminResource($this->repository->findOrFailWithRelations($id));
            },
            __('admin.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('admin.deleted_success')
        );
    }
}
