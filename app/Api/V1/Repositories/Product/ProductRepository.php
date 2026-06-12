<?php

namespace App\Api\V1\Repositories\Product;

use App\Admin\Repositories\Product\ProductRepository as AdminProductRepository;
use App\Api\V1\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductRepository extends AdminProductRepository implements ProductRepositoryInterface
{

    public function findOrFailWithRelations($id, array $relations = ['productAttributes', 'productVariations.attribute_variations'])
    {
        $this->findOrFail($id);
        if (in_array('productAttributes', $relations)) {
            $relations['productAttributes'] = function ($query) {
                return $query->with(['attribute', 'attribute_variations']);
            };
        }
        $this->instance = $this->instance->load($relations);
        return $this->instance;
    }

    public function getByCategoriesWithRelations(array $categories_id = [], array $relations = ['productVariations'], $limit = null, $page = null)
    {
        $query = $this->model->active()
            ->whereHas('categories', function ($query) use ($categories_id) {
                $query->whereIn('id', $categories_id);
            })
            ->with($relations)
            ->orderBy('id', 'desc');
        if ($limit && $page) {
            return $query->paginate($limit, ['*'], 'page', $page);
        } elseif ($limit) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    public function getSearchByKeysWithRelations(array $data)
    {
        $this->instance = $this->model->active();

        if (isset($data['keywords'])) {
            $this->instance = $this->instance->where('name', 'like', "%{$data['keywords']}%");
        }

        $this->instance = $this->instance
            ->orderBy('id', 'desc');

        // Kiểm tra và áp dụng phân trang
        $limit = isset($data['limit']) ? $data['limit'] : 10;

        // Sử dụng paginate để thực hiện phân trang
        $this->instance = $this->instance->where('is_active', 1)->paginate($limit);

        return $this->instance;
    }
    public function getProductsWithRelations(array $filterData = [], array $relations = ['categories', 'productVariations', 'productVariations.attribute_variations'], $desc = 'desc')
    {
        $this->instance = $this->model->where('is_active', 1)->with($relations);

        if (isset($filterData['min_product_price']) && isset($filterData['max_product_price'])) {
            $this->instance = $this->instance->where(function ($query) use ($filterData) {
                $query->whereBetween('promotion_price', [$filterData['min_product_price'], $filterData['max_product_price']])
                    ->orWhereHas('productVariations', function ($subQuery) use ($filterData) {
                        $subQuery->whereBetween('promotion_price', [$filterData['min_product_price'], $filterData['max_product_price']]);
                    });
            });
        }

        if (isset($filterData['category_ids'])) {
            $this->instance = $this->instance->whereHas('categories', function ($query) use ($filterData) {
                $query->whereIn('id', $filterData['category_ids']);
            });
        }

        if (isset($filterData['keyword'])) {
            $this->instance = $this->instance->where('name', 'Like', '%' . $filterData['keyword'] . '%');
        }

        $desc = $desc ?? 'asc';
        $this->instance = $this->instance->where('is_active', 1)->orderBy(function ($query) {
            $query->selectRaw('CASE
                WHEN type = 1 THEN promotion_price
                WHEN type = 2 THEN (SELECT promotion_price FROM products_variations WHERE products_variations.product_id = products.id ORDER BY promotion_price ASC LIMIT 1)
                ELSE promotion_price
            END');
        }, $desc)->paginate($filterData['limit']);
        return $this->instance;
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }

    public function searchProducts(Request $request)
    {
        $data = $request->validated();
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $searchTerm = $data['keyword'] ?? '';

        $query = $this->getQueryBuilder()->where('is_active', 1);

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    public function getRelatedProducts($id, $limit = 6)
    {
        // Lấy sản phẩm hiện tại và category của nó
        $product = $this->model->with('categories')->find($id);

        if (!$product) {
            // Nếu không tìm thấy sản phẩm, trả về random
            return $this->model
                ->where('is_active', 1)
                ->with('productVariations')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = collect();

        // Ưu tiên lấy sản phẩm từ cùng category nếu có category
        if (!empty($categoryIds)) {
            $relatedProducts = $this->model
                ->where('is_active', 1)
                ->where('id', '!=', $id)
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('id', $categoryIds);
                })
                ->with('productVariations')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Nếu không đủ 6 sản phẩm từ cùng category, lấy thêm sản phẩm random
        if ($relatedProducts->count() < $limit) {
            $relatedIds = $relatedProducts->pluck('id')->toArray();
            $relatedIds[] = $id; // Thêm id sản phẩm hiện tại để loại trừ

            $additionalProducts = $this->model
                ->where('is_active', 1)
                ->whereNotIn('id', $relatedIds)
                ->with('productVariations')
                ->inRandomOrder()
                ->limit($limit - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }

        return $relatedProducts->take($limit)->values();
    }

    public function getSuggestedProducts($limit = 6)
    {
        // Lấy sản phẩm có lượt bán cao (top sellers) rồi random trong số đó
        $bestSelling = $this->getBestSellingProducts($limit * 3); // Lấy nhiều hơn để có thể random

        // Load relations cho best selling products
        $bestSellingCollection = collect($bestSelling);
        if ($bestSellingCollection->isNotEmpty()) {
            $bestSellingIds = $bestSellingCollection->pluck('id')->toArray();
            // Tạo map để giữ lại total_sold
            $totalSoldMap = $bestSellingCollection->pluck('total_sold', 'id')->toArray();

            $bestSelling = $this->model
                ->whereIn('id', $bestSellingIds)
                ->with('productVariations')
                ->get()
                ->map(function ($product) use ($totalSoldMap) {
                    // Gán lại total_sold từ map
                    if (isset($totalSoldMap[$product->id])) {
                        $product->total_sold = $totalSoldMap[$product->id];
                    }
                    return $product;
                });
        } else {
            $bestSelling = collect([]);
        }

        if ($bestSelling->count() >= $limit) {
            // Nếu có đủ sản phẩm bán chạy, random trong số đó
            return $bestSelling->shuffle()->take($limit)->values();
        }

        // Nếu không đủ, lấy thêm sản phẩm random để đủ số lượng
        $bestSellingIds = $bestSelling->pluck('id')->toArray();
        $randomProducts = $this->model
            ->where('is_active', 1)
            ->whereNotIn('id', $bestSellingIds)
            ->with('productVariations')
            ->inRandomOrder()
            ->limit($limit - $bestSelling->count())
            ->get();

        // Kết hợp và shuffle
        return $bestSelling
            ->merge($randomProducts)
            ->shuffle()
            ->take($limit)
            ->values();
    }
}
