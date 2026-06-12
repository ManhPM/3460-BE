<?php

namespace App\Api\V1\Repositories\Product;

interface ProductRepositoryInterface
{
    public function findOrFail($id);

    public function getByCategoriesWithRelations(array $categories_id = [], array $relations = ['productVariations'], $limit = null, $page = null);

    public function findOrFailWithRelations($id, array $relations = []);

    public function getProductsWithRelations(array $filterData = [], array $relations = ['categories', 'productVariations', 'productVariations.attribute_variations'], $desc = 'desc');

    public function getSearchByKeysWithRelations(array $data);

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');

    public function getRelatedProducts($id, $limit = 6);

    public function getSuggestedProducts($limit = 6);
}
