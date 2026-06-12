<?php

namespace App\Api\AdminV1\Http\Controllers\FlashSale;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\FlashSale\FlashSaleRequest;
use App\Api\AdminV1\Http\Resources\FlashSale\FlashSaleResource;
use App\Api\AdminV1\Http\Resources\FlashSale\FlashSaleCollection;
use App\Api\AdminV1\Repositories\FlashSale\FlashSaleRepositoryInterface;
use App\Api\AdminV1\Services\FlashSale\FlashSaleService;

class FlashSaleController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        FlashSaleRepositoryInterface $repository,
        FlashSaleService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $flashSales = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new FlashSaleCollection($flashSales),
        ]);
    }

    public function show(int $id)
    {
        $flashSale = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new FlashSaleResource($flashSale->load([
                'details.product',
                'details.product_variation.attributeVariations'
            ]))
        ]);
    }

    public function store(FlashSaleRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $flashSale = $this->service->create($request->validated());
                return new FlashSaleResource($flashSale->load([
                    'details.product',
                    'details.product_variation.attributeVariations'
                ]));
            },
            __('flash_sale.created_success'),
            201
        );
    }

    public function update(FlashSaleRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $flashSale = $this->service->update($id, $request->validated());
                return new FlashSaleResource($flashSale->load([
                    'details.product',
                    'details.product_variation.attributeVariations'
                ]));
            },
            __('flash_sale.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('flash_sale.deleted_success')
        );
    }
}
