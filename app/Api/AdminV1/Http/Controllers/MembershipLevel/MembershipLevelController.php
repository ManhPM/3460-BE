<?php

namespace App\Api\AdminV1\Http\Controllers\MembershipLevel;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\MembershipLevel\MembershipLevelRequest;
use App\Api\AdminV1\Http\Resources\MembershipLevel\MembershipLevelResource;
use App\Api\AdminV1\Http\Resources\MembershipLevel\MembershipLevelCollection;
use App\Api\AdminV1\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Api\AdminV1\Services\MembershipLevel\MembershipLevelService;

class MembershipLevelController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        MembershipLevelRepositoryInterface $repository,
        MembershipLevelService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $membershipLevels = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new MembershipLevelCollection($membershipLevels),
        ]);
    }

    public function show(int $id)
    {
        $membershipLevel = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new MembershipLevelResource($membershipLevel->loadCount('users'))
        ]);
    }

    public function store(MembershipLevelRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $membershipLevel = $this->service->create($request->validated());
                return new MembershipLevelResource($membershipLevel);
            },
            __('membership_level.created_success'),
            201
        );
    }

    public function update(MembershipLevelRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $membershipLevel = $this->service->update($id, $request->validated());
                return new MembershipLevelResource($membershipLevel);
            },
            __('membership_level.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('membership_level.deleted_success')
        );
    }
}
