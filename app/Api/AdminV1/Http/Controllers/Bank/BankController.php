<?php

namespace App\Api\AdminV1\Http\Controllers\Bank;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Bank\BankRequest;
use App\Api\AdminV1\Http\Resources\Bank\BankResource;
use App\Api\AdminV1\Http\Resources\Bank\BankCollection;
use App\Api\AdminV1\Repositories\Bank\BankRepositoryInterface;
use App\Api\AdminV1\Services\Bank\BankService;

class BankController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        BankRepositoryInterface $repository,
        BankService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $banks = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new BankCollection($banks),
        ]);
    }

    public function show(int $id)
    {
        $bank = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new BankResource($bank)
        ]);
    }

    public function store(BankRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $bank = $this->service->create($request->validated());
                return new BankResource($bank);
            },
            __('bank.created_success'),
            201
        );
    }

    public function update(BankRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $bank = $this->service->update($id, $request->validated());
                return new BankResource($bank);
            },
            __('bank.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('bank.deleted_success')
        );
    }

    public function listUnique()
    {
        $banks = $this->repository->getUniqueBanks();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => BankResource::collection($banks),
        ]);
    }
}
