<?php

namespace App\Api\AdminV1\Http\Controllers\UserAddress;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\UserAddress\UserAddressRequest;
use App\Admin\Repositories\UserAddress\UserAddressRepositoryInterface;

class UserAddressController extends Controller
{
    protected $repository;

    public function __construct(
        UserAddressRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function index(int $userId)
    {
        $addresses = $this->repository->getBy(['user_id' => $userId]);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $addresses,
        ]);
    }

    public function show(int $id)
    {
        $address = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $address->load(['user', 'province', 'ward'])
        ]);
    }

    public function store(UserAddressRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $address = $this->repository->create($request->validated());
                return $address->load(['user', 'province', 'ward']);
            },
            __('user_address.created_success'),
            201
        );
    }

    public function update(UserAddressRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $request->merge(['id' => $id]);
                $address = $this->repository->update($id, $request->validated());
                return $address->load(['user', 'province', 'ward']);
            },
            __('user_address.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->repository->delete($id);
            },
            __('user_address.deleted_success')
        );
    }

    public function setDefault(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $address = $this->repository->findOrFail($id);
                $this->repository->update($id, ['is_default' => 1]);

                $addresses = $this->repository->getBy(['user_id' => $address->user_id]);
                foreach ($addresses as $addr) {
                    if ($addr->id != $id) {
                        $this->repository->update($addr->id, ['is_default' => 0]);
                    }
                }

                return $address->fresh()->load(['user', 'province', 'ward']);
            },
            __('user_address.set_default_success')
        );
    }
}
