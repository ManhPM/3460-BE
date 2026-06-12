<?php

namespace App\Api\AdminV1\Http\Controllers\VoucherProgram;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\VoucherProgram\VoucherProgramRequest;
use App\Api\AdminV1\Http\Requests\VoucherProgram\GiveVoucherRequest;
use App\Api\AdminV1\Http\Requests\VoucherProgram\ResetVoucherProgramRequest;
use App\Api\AdminV1\Http\Resources\VoucherProgram\VoucherProgramResource;
use App\Api\AdminV1\Http\Resources\VoucherProgram\VoucherProgramCollection;
use App\Api\AdminV1\Repositories\VoucherProgram\VoucherProgramRepositoryInterface;
use App\Api\AdminV1\Services\VoucherProgram\VoucherProgramService;
use App\Admin\Services\VoucherProgram\VoucherProgramServiceInterface;

class VoucherProgramController extends Controller
{
    protected $repository;
    protected $service;
    protected $adminService;

    public function __construct(
        VoucherProgramRepositoryInterface $repository,
        VoucherProgramService $service,
        VoucherProgramServiceInterface $adminService
    ) {
        $this->repository = $repository;
        $this->service = $service;
        $this->adminService = $adminService;
    }

    public function index()
    {
        $programs = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new VoucherProgramCollection($programs),
        ]);
    }

    public function show(int $id)
    {
        $program = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new VoucherProgramResource($program)
        ]);
    }

    public function store(VoucherProgramRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $program = $this->service->create($request->validated());
                return new VoucherProgramResource($program);
            },
            __('voucher_program.created_success'),
            201
        );
    }

    public function update(VoucherProgramRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $program = $this->service->update($id, $request->validated());
                return new VoucherProgramResource($program);
            },
            __('voucher_program.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('voucher_program.deleted_success')
        );
    }

    public function giveVoucher(GiveVoucherRequest $request)
    {
        return $this->handleResponse(
            function () use ($request) {
                $result = $this->adminService->giveVoucher($request);
                return $result;
            },
            __('voucher_program.give_voucher_success')
        );
    }

    public function reset(ResetVoucherProgramRequest $request)
    {
        return $this->handleResponse(
            function () use ($request) {
                $result = $this->adminService->reset($request);
                return $result;
            },
            __('voucher_program.reset_success')
        );
    }

    public function toggleStatus(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $program = $this->service->toggleStatus($id);
                return new VoucherProgramResource($program);
            },
            __('voucher_program.status_updated_success')
        );
    }
}
