<?php

namespace App\Api\AdminV1\Http\Controllers\WalletTransaction;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\WalletTransaction\WalletTransactionRequest;
use App\Api\AdminV1\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Api\AdminV1\Http\Resources\WalletTransaction\WalletTransactionCollection;
use App\Api\AdminV1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Api\AdminV1\Services\WalletTransaction\WalletTransactionService;

class WalletTransactionController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        WalletTransactionRepositoryInterface $repository,
        WalletTransactionService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $transactions = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new WalletTransactionCollection($transactions),
        ]);
    }

    public function show(int $id)
    {
        $transaction = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new WalletTransactionResource($transaction->load('user'))
        ]);
    }

    public function store(WalletTransactionRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $transaction = $this->service->create($request->validated());
                return new WalletTransactionResource($transaction->load('user'));
            },
            __('wallet_transaction.created_success'),
            201
        );
    }

    public function update(WalletTransactionRequest $request, int $id)
    {
        $transaction = $this->repository->findOrFail($id);

        // Không cho phép update nếu đã duyệt hoặc đã hủy
        if ($transaction->status === 'approved' || $transaction->status === 'rejected') {
            return response()->json([
                'status' => 400,
                'message' => __('wallet_transaction.already_processed', ['status' => $transaction->status === 'approved' ? __('approved') : __('rejected')]),
            ], 400);
        }

        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                // Chỉ update note theo logic Admin Service
                $data = $request->validated();
                $transaction = $this->service->update($id, $data);
                return new WalletTransactionResource($transaction->load('user'));
            },
            __('wallet_transaction.note_updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('wallet_transaction.deleted_success')
        );
    }

    public function approve(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $result = $this->service->approve($id);
                if (!$result) {
                    return response()->json([
                        'status' => 400,
                        'message' => __('wallet_transaction.not_pending', ['action' => __('duyệt')]),
                    ], 400);
                }
                $transaction = $this->repository->findOrFail($id);
                return new WalletTransactionResource($transaction->load('user'));
            },
            __('wallet_transaction.approved_success')
        );
    }

    public function reject(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $result = $this->service->reject($id);
                if (!$result) {
                    return response()->json([
                        'status' => 400,
                        'message' => __('wallet_transaction.not_pending', ['action' => __('từ chối')]),
                    ], 400);
                }
                $transaction = $this->repository->findOrFail($id);
                return new WalletTransactionResource($transaction->load('user'));
            },
            __('wallet_transaction.rejected_success')
        );
    }
}
