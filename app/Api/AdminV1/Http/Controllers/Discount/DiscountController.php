<?php

namespace App\Api\AdminV1\Http\Controllers\Discount;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Discount\DiscountRequest;
use App\Api\AdminV1\Http\Resources\Discount\DiscountResource;
use App\Api\AdminV1\Http\Resources\Discount\DiscountCollection;
use App\Api\AdminV1\Repositories\Discount\DiscountRepositoryInterface;
use App\Api\AdminV1\Services\Discount\DiscountService;

class DiscountController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        DiscountRepositoryInterface $repository,
        DiscountService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $discounts = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new DiscountCollection($discounts),
        ]);
    }

    public function show(int $id)
    {
        $discount = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new DiscountResource($discount)
        ]);
    }

    public function store(DiscountRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $discount = $this->service->create($request->validated());
                return new DiscountResource($discount);
            },
            __('discount.created_success'),
            201
        );
    }

    public function update(DiscountRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $discount = $this->service->update($id, $request->validated());
                return new DiscountResource($discount);
            },
            __('discount.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('discount.deleted_success')
        );
    }
}
