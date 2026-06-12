<?php

namespace App\Admin\Repositories\Product;

use App\Admin\Repositories\EloquentRepositoryInterface;
use App\Models\Discount;
use App\Models\Product;

interface ProductRepositoryInterface extends EloquentRepositoryInterface
{
    public function getByIdsAndOrderByIds(array $ids);
    public function getByColumnsWithRelationsLimit(array $data, array $relations = ['productVariations.attribute_variations'], $limit = 10);

    public function getAllByColumns(array $data, $type = 'detail');

    public function deleteProductAttributes(Product $product);

    public function deleteProductVariations(Product $product);

    public function loadRelations(Product $product, array $relations = []);

    public function attachCategories(Product $product, array $categoriesId);

    public function syncCategories(Product $product, array $categoriesId);

    public function getQueryBuilderWithRelations($relations = ['categories', 'productVariations']);
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');

    public function searchAllLimit($value = '', $meta = [], $select = [], $limit = 10);
    public function attachToppings(Product $product, array $toppingsId);
    public function syncToppings(Product $product, array $toppingsId);

    public function attachDiscounts(Product $product, array $discountIds);

    public function syncDiscounts(Product $product, array $discountIds);
    public function getRelatedProducts($id);

    public function getMinMaxPromotionPrices($relations = ['productVariations']);

    public function getProductsWithRelations(array $filterData = [], array $relations = ['categories', 'productVariations', 'productVariations.attribute_variations'], $desc = 'desc');

    public function getFlashSaleProductsWithRelations(array $relations = ['categories', 'productVariations']);
    public function findOrFailBySlug($slug);
    public function getRecommendation($userId);
    public function getBestSellingProducts($limit = 8);
    public function getBySlugs(array $slugs);
}
