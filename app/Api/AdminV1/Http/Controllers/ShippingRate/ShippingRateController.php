<?php

namespace App\Api\AdminV1\Http\Controllers\ShippingRate;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\ShippingRate\ShippingRateRequest;
use App\Api\AdminV1\Http\Resources\ShippingRate\ShippingRateResource;
use App\Api\AdminV1\Http\Resources\ShippingRate\ShippingRateCollection;
use App\Api\AdminV1\Repositories\ShippingRate\ShippingRateRepositoryInterface;
use App\Api\AdminV1\Services\ShippingRate\ShippingRateService;

class ShippingRateController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        ShippingRateRepositoryInterface $repository,
        ShippingRateService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $shippingRates = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new ShippingRateCollection($shippingRates),
        ]);
    }

    public function show(int $id)
    {
        $shippingRate = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new ShippingRateResource($shippingRate->load(['province', 'ward']))
        ]);
    }

    public function store(ShippingRateRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $shippingRate = $this->service->create($request->validated());
                return new ShippingRateResource($shippingRate->load(['province', 'ward']));
            },
            __('shipping_rate.created_success'),
            201
        );
    }

    public function update(ShippingRateRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $shippingRate = $this->service->update($id, $request->validated());
                return new ShippingRateResource($shippingRate->load(['province', 'ward']));
            },
            __('shipping_rate.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('shipping_rate.deleted_success')
        );
    }
}
