<?php

namespace App\Api\AdminV1\Http\Controllers\CommissionWithdrawal;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\CommissionWithdrawal\CommissionWithdrawalRequest;
use App\Api\AdminV1\Http\Resources\CommissionWithdrawal\CommissionWithdrawalResource;
use App\Api\AdminV1\Http\Resources\CommissionWithdrawal\CommissionWithdrawalCollection;
use App\Api\AdminV1\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Api\AdminV1\Services\CommissionWithdrawal\CommissionWithdrawalService;

class CommissionWithdrawalController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        CommissionWithdrawalRepositoryInterface $repository,
        CommissionWithdrawalService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $withdrawals = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new CommissionWithdrawalCollection($withdrawals),
        ]);
    }

    public function show(int $id)
    {
        $withdrawal = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new CommissionWithdrawalResource($withdrawal->load('user'))
        ]);
    }

    public function updateStatus(CommissionWithdrawalRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $validated = $request->validated();
                $withdrawal = $this->service->updateStatus($id, $validated['status'] ?? $request->input('status'), $validated['note'] ?? $request->input('note'));
                return new CommissionWithdrawalResource($withdrawal->load('user'));
            },
            __('commission_withdrawal.status_updated_success')
        );
    }
}
