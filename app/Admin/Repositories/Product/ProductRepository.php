<?php

namespace App\Admin\Repositories\Product;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductRepository extends EloquentRepository implements ProductRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return Product::class;
    }
    public function getByIdsAndOrderByIds(array $ids)
    {
        $this->instance = $this->model
            ->whereIn('id', $ids)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $ids) . ")"))
            ->get();

        return $this->instance;
    }

    public function getBySlugs(array $slugs)
    {
        $this->instance = $this->model
            ->whereIn('slug', $slugs)
            ->get();

        return $this->instance;
    }

    public function getRelatedProducts($id)
    {
        $this->instance = $this->model
            ->where('id', '!=', $id)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->take(8)
            ->get();
        return $this->instance;
    }

    public function getBestSellingProducts($limit = 8)
    {
        $data = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'order_details.product_id',
                DB::raw('SUM(order_details.qty) as total_qty'),
                DB::raw('SUM(order_details.unit_price * order_details.qty) as total_revenue')
            )
            ->where('orders.status', OrderStatus::Completed)
            ->groupBy('order_details.product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();

        $bestSellingProducts = [];
        foreach ($data as $item) {
            $product = $this->model->find($item->product_id);
            $product['total_sold'] = $item->total_qty;
            if ($product->is_active) {
                $bestSellingProducts[] = $product;
            }
        }

        // Nếu chưa có sản phẩm bán chạy nào, lấy random
        if (empty($bestSellingProducts)) {
            $bestSellingProducts = $this->model
                ->where('is_active', 1)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        return $bestSellingProducts;
    }


    public function getAllByColumns(array $data, $type = 'detail')
    {
        $this->getQueryBuilder();
        foreach ($data as $key => $value) {
            if ($key == 'name') {
                $this->instance = $this->instance->where($key, 'like', "%{$value}%");
            } else {
                $this->instance = $this->instance->where($key, $value);
            }
        }

        if ($type == 'flashsale') {
            $now = Carbon::now();
            $this->instance = $this->instance->where('is_contact_price', 0)->whereDoesntHave('flash_sales', function ($query) use ($now) {
                $query->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->whereRaw('qty > sold');
            });
        }

        $this->instance = $this->instance->where('is_active', 1)->get();
        return $this->instance;
    }

    public function getByColumnsWithRelationsLimit(array $data, array $relations = ['productVariations.attribute_variations.attribute'], $limit = 10)
    {
        $this->getQueryBuilderWithRelations($relations);

        foreach ($data as $key => $value) {
            if ($key == 'name') {
                $this->instance = $this->instance->where($key, 'like', "%{$value}%");
            } else {
                $this->instance = $this->instance->where($key, $value);
            }
        }

        $this->instance = $this->instance->where('is_active', 1)->limit($limit)->get();
        return $this->instance;
    }

    public function attachCategories(Product $product, array $categoriesId)
    {
        return $product->categories()->attach($categoriesId);
    }

    public function syncCategories(Product $product, array $categoriesId)
    {
        return $product->categories()->sync($categoriesId);
    }

    public function attachDiscounts(Product $product, array $discountIds)
    {
        return $product->discounts()->attach($discountIds);
    }

    public function syncDiscounts(Product $product, array $discountIds)
    {
        return $product->discounts()->sync($discountIds);
    }

    public function attachToppings(Product $product, array $toppingsId)
    {
        return $product->toppings()->attach($toppingsId);
    }

    public function syncToppings(Product $product, array $toppingsId)
    {
        return $product->toppings()->sync($toppingsId);
    }
    public function deleteProductAttributes(Product $product)
    {
        $product->productAttributes()->delete();
    }
    public function deleteProductVariations(Product $product)
    {
        $product->productVariations()->delete();
    }
    public function loadRelations(Product $product, array $relations = [])
    {
        return $product->load($relations);
    }
    public function getQueryBuilderWithRelations($relations = ['categories', 'productVariations'])
    {
        $this->getQueryBuilderOrderBy();
        $this->instance = $this->instance->with($relations);
        return $this->instance;
    }

    public function getRecommendation($userId = 0)
    {
        if ($userId) {
            // Lấy danh sách sản phẩm mà người dùng đã đánh giá
            $userReviews = Review::where('user_id', $userId)->pluck('product_id', 'rating')->toArray();

            if (empty($userReviews)) {
                // Nếu không có đánh giá, trả về danh sách ngẫu nhiên
                return $this->model->where('is_active', 1)->inRandomOrder()->limit(12)->get();
            }

            // Tạo ma trận sản phẩm và điểm đánh giá
            $allReviews = Review::all();
            $matrix = [];
            foreach ($allReviews as $review) {
                $matrix[$review->user_id][$review->product_id] = $review->rating;
            }

            // Tính toán độ tương đồng giữa các sản phẩm
            $similarities = [];
            foreach ($userReviews as $userProduct => $userRating) {
                foreach ($matrix as $otherUser => $ratings) {
                    if (isset($ratings[$userProduct])) {
                        foreach ($ratings as $product => $rating) {
                            if ($product != $userProduct) {
                                $similarities[$product] = ($similarities[$product] ?? 0) + $rating * $userRating;
                            }
                        }
                    }
                }
            }

            // Sắp xếp sản phẩm theo độ tương đồng
            arsort($similarities);

            // Lấy danh sách sản phẩm gợi ý
            $recommendedProductIds = array_keys(array_slice($similarities, 0, 12, true));

            return $this->model->where('is_active', 1)->whereIn('id', $recommendedProductIds)->get();
        } else {
            return $this->model->where('is_active', 1)->inRandomOrder()->limit(12)->get();
        }
    }



    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
    public function getMinMaxPromotionPrices($relations = ['productVariations']): array
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->where('is_active', 1)->with($relations);
        $products = $this->instance->get();

        $allPrices = collect();

        foreach ($products as $product) {
            // Thêm giá của sản phẩm chính
            $allPrices->push($product->promotion_price);

            // Thêm giá của các biến thể sản phẩm
            if ($product->productVariations) {
                $allPrices = $allPrices->concat($product->productVariations->pluck('promotion_price'));
            }
        }

        // Lọc bỏ các giá trị null hoặc 0 (nếu cần)
        $allPrices = $allPrices->filter();

        return [
            'min_product_price' => $allPrices->min(),
            'max_product_price' => $allPrices->max(),
        ];
    }

    public function getProductsWithRelations(array $filterData = [], array $relations = ['categories', 'productVariations', 'productVariations.attribute_variations'], $desc = 'desc')
    {
        $this->instance = $this->instance->where('is_active', 1)->with($relations);

        if (isset($filterData['min_product_price']) && isset($filterData['max_product_price'])) {
            $this->instance = $this->instance->where(function ($query) use ($filterData) {
                $query->whereBetween('promotion_price', [$filterData['min_product_price'], $filterData['max_product_price']])
                    ->orWhereHas('productVariations', function ($subQuery) use ($filterData) {
                        $subQuery->whereBetween('promotion_price', [$filterData['min_product_price'], $filterData['max_product_price']]);
                    });
            });
        }

        if (isset($filterData['category_slug'])) {
            $this->instance = $this->instance->whereHas('categories', function ($query) use ($filterData) {
                $query->where('slug', $filterData['category_slug']);
            });
        }

        if (isset($filterData['keyword'])) {
            $this->instance = $this->instance->where('name', 'Like', '%' . $filterData['key'] . '%');
        }

        // Kiểm tra điều kiện qty theo type
        $this->instance = $this->instance->where(function ($query) {
            $query->where(function ($q) {
                $q->where('type', 1)->where('qty', '>', 0);
            })->orWhere(function ($q) {
                $q->where('type', 2)->whereHas('productVariations', function ($subQuery) {
                    $subQuery->where('qty', '>', 0);
                });
            });
        });

        $desc = $desc ?? 'asc';
        $this->instance = $this->instance->orderBy(function ($query) {
            $query->selectRaw('CASE
                WHEN type = 1 THEN promotion_price
                WHEN type = 2 THEN (SELECT promotion_price FROM products_variations WHERE products_variations.product_id = products.id ORDER BY promotion_price ASC LIMIT 1)
                ELSE promotion_price
            END');
        }, $desc)->paginate($filterData['limit']);
        return $this->instance;
    }

    public function getFlashSaleProductsWithRelations(array $relations = ['categories', 'productVariations'])
    {
        $this->instance = $this->loadRelations($this->model, $relations);
        $this->instance = $this->instance->where('is_active', 1)->orderBy('promotion_price', 'ASC')->paginate(8);

        return $this->instance;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'sku', 'name', 'price', 'promotion_price', 'slug', 'avatar'], $limit = 10)
    {
        $this->instance = $this->model->where('is_active', 1)->with('productVariations')->select($select);
        $this->getQueryBuilderFindByKey($keySearch);

        foreach ($meta as $key => $value) {
            if ($key !== 'page') {
                $this->instance = $this->instance->where($key, $value);
            }
        }

        return $this->instance->paginate($limit);
    }

    protected function getQueryBuilderFindByKey($key)
    {
        $this->instance = $this->instance->where(function ($query) use ($key) {
            return $query->where('name', 'LIKE', '%' . $key . '%')
                ->orWhere('price', 'LIKE', '%' . $key . '%');
        });
    }
    public function findOrFailBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }
}
