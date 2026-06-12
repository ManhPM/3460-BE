<?php

namespace App\Api\AdminV1\Http\Controllers\Voucher;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Voucher\VoucherRequest;
use App\Api\AdminV1\Http\Resources\Voucher\VoucherResource;
use App\Api\AdminV1\Http\Resources\Voucher\VoucherCollection;
use App\Api\AdminV1\Repositories\Voucher\VoucherRepositoryInterface;
use App\Api\AdminV1\Services\Voucher\VoucherService;

class VoucherController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        VoucherRepositoryInterface $repository,
        VoucherService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $vouchers = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new VoucherCollection($vouchers),
        ]);
    }

    public function show(int $id)
    {
        $voucher = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new VoucherResource($voucher->load('user'))
        ]);
    }

    public function store(VoucherRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $voucher = $this->service->create($request->validated());
                return new VoucherResource($voucher->load('user'));
            },
            __('voucher.created_success'),
            201
        );
    }

    public function update(VoucherRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $voucher = $this->service->update($id, $request->validated());
                return new VoucherResource($voucher->load('user'));
            },
            __('voucher.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('voucher.deleted_success')
        );
    }

    public function toggleStatus(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $voucher = $this->service->toggleStatus($id);
                return new VoucherResource($voucher->load('user'));
            },
            __('voucher.status_updated_success')
        );
    }
}
