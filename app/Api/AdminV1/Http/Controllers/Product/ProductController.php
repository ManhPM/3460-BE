<?php

namespace App\Api\AdminV1\Http\Controllers\Product;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Product\ProductRequest;
use App\Api\AdminV1\Http\Requests\Product\UpdateProductAttributesRequest;
use App\Api\AdminV1\Http\Requests\Product\UpdateProductVariationsRequest;
use App\Api\AdminV1\Http\Resources\Product\ProductResource;
use App\Api\AdminV1\Http\Resources\Product\ProductCollection;
use App\Api\AdminV1\Repositories\Product\ProductRepositoryInterface;
use App\Api\AdminV1\Services\Product\ProductService;

class ProductController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        ProductRepositoryInterface $repository,
        ProductService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Display a listing of products
     */
    public function index()
    {
        $products = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new ProductCollection($products),
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(ProductRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $product = $this->service->create($request->validated());
                return new ProductResource($this->repository->findOrFailWithRelations($product->id));
            },
            __('product.created_success'),
            201
        );
    }

    /**
     * Display the specified product
     */
    public function show(int $id)
    {
        $product = $this->repository->findOrFailWithRelations($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(ProductRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $product = $this->service->update($id, $request->validated());
                return new ProductResource($this->repository->findOrFailWithRelations($id));
            },
            __('product.updated_success')
        );
    }

    /**
     * Remove the specified product
     */
    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('product.deleted_success')
        );
    }

    /**
     * Duplicate product
     */
    public function duplicate(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $newProduct = $this->service->duplicate($id);
                return new ProductResource($this->repository->findOrFailWithRelations($newProduct->id));
            },
            __('product.duplicated_success')
        );
    }

    /**
     * Update product attributes
     */
    public function updateAttributes(UpdateProductAttributesRequest $request, int $product)
    {
        return $this->handleResponse(
            function () use ($request, $product) {
                $data = $request->validated();
                $this->service->updateAttributes($product, $data['product_attribute']);
                return new ProductResource($this->repository->findOrFailWithRelations($product));
            },
            __('product.attributes_updated_success')
        );
    }

    /**
     * Update product variations
     */
    public function updateVariations(UpdateProductVariationsRequest $request, int $product)
    {
        return $this->handleResponse(
            function () use ($request, $product) {
                $data = $request->validated();
                $this->service->updateVariations($product, $data['products_variations']);
                return new ProductResource($this->repository->findOrFailWithRelations($product));
            },
            __('product.variations_updated_success')
        );
    }
}
