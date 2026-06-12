<?php

namespace App\Api\AdminV1\Services\Product;

use App\Api\AdminV1\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Repositories\Product\ProductAttributeRepositoryInterface;
use App\Admin\Repositories\Product\ProductVariationRepositoryInterface;

class ProductService
{
    protected $repository;
    protected $productAttributeRepository;
    protected $productVariationRepository;

    public function __construct(
        ProductRepositoryInterface $repository,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductVariationRepositoryInterface $productVariationRepository
    ) {
        $this->repository = $repository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productVariationRepository = $productVariationRepository;
    }

    public function create(array $data)
    {
        // Transform data from ['product' => [...], 'categories_id' => [...]] to flat array
        $productData = [];

        // Extract product data
        if (isset($data['product']) && is_array($data['product'])) {
            $productData = $data['product'];
        } else {
            $productData = $data;
        }

        // Handle gallery - convert string to array if needed
        if (isset($productData['gallery']) && is_string($productData['gallery'])) {
            $productData['gallery'] = !empty($productData['gallery'])
                ? explode(',', $productData['gallery'])
                : [];
        }

        // Handle categories_id separately (will be synced in controller or service)
        $categoriesId = $data['categories_id'] ?? [];

        // Create product
        $product = $this->repository->create($productData);

        // Sync categories if provided
        if (!empty($categoriesId)) {
            $product->categories()->sync($categoriesId);
        }

        return $product;
    }

    public function update(int $id, array $data)
    {
        // Transform data from ['product' => [...], 'categories_id' => [...]] to flat array
        $productData = [];

        // Extract product data
        if (isset($data['product']) && is_array($data['product'])) {
            $productData = $data['product'];
        } else {
            $productData = $data;
        }

        // Remove id from productData if present (already passed as $id parameter)
        unset($productData['id']);

        // Handle gallery - convert string to array if needed
        if (isset($productData['gallery']) && is_string($productData['gallery'])) {
            $productData['gallery'] = !empty($productData['gallery'])
                ? explode(',', $productData['gallery'])
                : [];
        }

        // Handle categories_id separately (will be synced in controller or service)
        $categoriesId = $data['categories_id'] ?? null;

        // Update product
        $product = $this->repository->update($id, $productData);

        // Sync categories if provided
        if ($categoriesId !== null) {
            $product->categories()->sync($categoriesId);
        }

        return $product;
    }

    public function delete(int $id)
    {
        $product = $this->repository->findOrFail($id);
        $product->update(['is_deleted' => 1]);
        return true;
    }

    /**
     * Duplicate a product
     */
    public function duplicate(int $id)
    {
        return $this->repository->duplicate($id);
    }

    /**
     * Update product attributes
     */
    public function updateAttributes(int $productId, array $productAttribute)
    {
        // Delete existing attributes not in the new list
        $existingAttributes = \App\Models\ProductAttribute::where('product_id', $productId)->get();
        $newAttributeIds = $productAttribute['attribute_id'] ?? [];

        foreach ($existingAttributes as $existing) {
            if (!in_array($existing->attribute_id, $newAttributeIds)) {
                $existing->delete();
            }
        }

        // Convert attribute_variation_id from attribute_id keyed to index keyed
        $attributeVariationIdIndexed = [];
        foreach ($productAttribute['attribute_id'] as $index => $attributeId) {
            if (isset($productAttribute['attribute_variation_id'][$attributeId])) {
                $attributeVariationIdIndexed[$index] = $productAttribute['attribute_variation_id'][$attributeId];
            } else {
                $attributeVariationIdIndexed[$index] = [];
            }
        }

        $productAttribute['attribute_variation_id'] = $attributeVariationIdIndexed;

        // Create or update attributes
        $this->productAttributeRepository->createOrUpdateWithVariationApi($productId, $productAttribute);

        return true;
    }

    /**
     * Update product variations
     */
    public function updateVariations(int $productId, array $productVariations)
    {
        // Handle qty and image if not provided
        if (!isset($productVariations['qty'])) {
            $productVariations['qty'] = [];
        }
        if (!isset($productVariations['image'])) {
            $productVariations['image'] = [];
        }

        // Ensure all keys have values - keys are like "variation_0", "variation_1", etc.
        $keys = array_keys($productVariations['attribute_variation_id']);
        $processedVariations = [
            'id' => [],
            'price' => [],
            'promotion_price' => [],
            'image' => [],
            'attribute_variation_id' => []
        ];

        foreach ($keys as $key) {
            $processedVariations['id'][$key] = isset($productVariations['id'][$key]) && $productVariations['id'][$key] > 0
                ? $productVariations['id'][$key]
                : 0; // New variation if id is 0 or not set
            $processedVariations['price'][$key] = $productVariations['price'][$key] ?? 0;
            $processedVariations['promotion_price'][$key] = $productVariations['promotion_price'][$key] ?? 0;
            $processedVariations['image'][$key] = $productVariations['image'][$key] ?? null;
            $processedVariations['attribute_variation_id'][$key] = $productVariations['attribute_variation_id'][$key] ?? [];
        }

        // Update variations
        $this->productVariationRepository->createOrUpdateWithVariation($productId, $processedVariations);

        return true;
    }
}
