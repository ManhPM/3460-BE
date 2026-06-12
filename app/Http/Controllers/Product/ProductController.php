<?php

namespace App\Http\Controllers\Product;

use App;
use App\Admin\Repositories\Review\ReviewRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Admin\Http\Resources\Product\ProductEditResource;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Services\Product\ProductServiceInterface;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Admin\Repositories\Attribute\AttributeRepositoryInterface;
use App\Admin\Repositories\AttributeVariation\AttributeVariationRepositoryInterface;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use Illuminate\Http\Request;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Http\Requests\Review\ReviewRequest;
use App\Admin\Services\Review\ReviewServiceInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Api\V1\Http\Resources\Product\ProductVariationResource;
use App\Models\Product;

class ProductController extends Controller
{
    protected FlashSaleRepositoryInterface $flashSaleRepository;
    protected CategoryRepositoryInterface $repositoryCategory;
    protected AttributeRepositoryInterface $repositoryAttribute;
    protected AttributeVariationRepositoryInterface $repositoryAttributeVariation;
    protected DiscountRepositoryInterface $discountRepository;
    protected SettingRepositoryInterface $settingRepository;
    protected ReviewServiceInterface $reviewService;
    protected ReviewRepositoryInterface $reviewRepository;
    protected OrderRepositoryInterface $orderRepository;
    protected OrderDetailRepositoryInterface $orderDetailRepository;
    protected Product $model;

    public function __construct(
        ProductRepositoryInterface $repository,
        FlashSaleRepositoryInterface $flashSaleRepository,
        DiscountRepositoryInterface $discountRepository,
        CategoryRepositoryInterface $repositoryCategory,
        AttributeRepositoryInterface $repositoryAttribute,
        AttributeVariationRepositoryInterface $repositoryAttributeVariation,
        SettingRepositoryInterface $settingRepository,
        ProductServiceInterface $service,
        ReviewServiceInterface $reviewService,
        ReviewRepositoryInterface $reviewRepository,
        OrderRepositoryInterface $orderRepository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        Product $model,
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->flashSaleRepository = $flashSaleRepository;
        $this->repositoryCategory = $repositoryCategory;
        $this->repositoryAttribute = $repositoryAttribute;
        $this->repositoryAttributeVariation = $repositoryAttributeVariation;
        $this->discountRepository = $discountRepository;
        $this->settingRepository = $settingRepository;
        $this->service = $service;
        $this->reviewService = $reviewService;
        $this->reviewRepository = $reviewRepository;
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->model = $model;
    }

    public function getView(): array
    {
        return [
            'indexUser' => 'user.products.index',
            'sale-limited' => 'user.products.sale-limited',
            'product-detail' => 'user.products.product-detail',
            'product-modal' => 'components.quickview',
        ];
    }

    public function getRoute(): array
    {
        return [
            'home' => 'user.index'
        ];
    }

    public function indexUser(Request $request)
    {
        $settings = $this->settingRepository->getAll();
        $title = $settings->where('setting_key', 'product_title')->first()->plain_value;
        $meta_desc = $settings->where('setting_key', 'product_meta_desc')->first()->plain_value;
        $categories = $this->repositoryCategory->getFlatTree();
        $colors = $this->repositoryAttribute->findByField('slug', 'mau-sac');
        $sizes = $this->repositoryAttribute->findByField('slug', 'kich-thuoc');
        $minMax = $this->repository->getMinMaxPromotionPrices();

        $filter = [
            'min_product_price' => $request->input('min_product_price'),
            'max_product_price' => $request->input('max_product_price'),
            'category_slug' => $request->input('category_ids'),
            'color_slug' => $request->input('color_slugs'),
            'size_slug' => $request->input('size_slugs'),
            'key' => $request->input('key'),
            'limit' => 16
        ];

        $products = $this->repository->getProductsWithRelations($filter, [], $request->input('sort'));
        return view($this->view['indexUser'], [
            'categories' => $categories,
            'colors' => $colors,
            'sort' => $request->input('sort') ?? null,
            'key' => $request->input('key') ?? null,
            'sizes' => $sizes,
            'minMax' => $minMax,
            'products' => $products,
            'title' => $title,
            'meta_desc' => $meta_desc,
            'breadcrumbs' => $this->crums->add(__('Sản phẩm'))->getBreadcrumbs(),
        ]);
    }

    function handleAffiliateCode(Request $request, $slug)
    {
        if (!$request->input('affiliate_code')) {
            return;
        }

        $affiliateList = session()->get('affiliate_list', []);
        $affiliate_code = $request->input('affiliate_code');
        $currentAffiliateCode = auth()->check() ? auth()->user()->affiliate_code : null;

        // Nếu user đã đăng nhập và affiliate_code trùng với của user, không làm gì
        if (auth()->check() && $affiliate_code === $currentAffiliateCode) {
            return;
        }

        // Tìm slug trong danh sách
        $existingKey = collect($affiliateList)->search(fn($item) => $item['slug'] === $slug);

        if ($existingKey === false) {
            // Nếu chưa có `slug`, thêm mới
            $affiliateList[] = [
                'affiliate_code' => $affiliate_code,
                'slug' => $slug,
            ];
        } else {
            // Nếu có `slug`, cập nhật `affiliate_code` nếu khác
            if ($affiliateList[$existingKey]['affiliate_code'] !== $affiliate_code) {
                $affiliateList[$existingKey]['affiliate_code'] = $affiliate_code;
            }
        }

        // Lưu danh sách cập nhật vào session
        session()->put('affiliate_list', $affiliateList);
    }


    public function detail($slug, Request $request)
    {
        $this->handleAffiliateCode($request, $slug);
        $product = $this->repository->loadRelations($this->repository->findOrFailBySlug($slug), [
            'categories:id,name',
            'productAttributes' => function ($query) {
                return $query->with(['attribute.variations', 'attribute_variations:id']);
            },
            'productVariations.attribute_variations'
        ]);
        $randomProducts = $this->repository->getRelatedProducts($product->id);
        $product = new ProductEditResource($product);
        return view($this->view['product-detail'], [
            'product' => $product,
            'breadcrumbs' => $this->crums->add(__('Sản phẩm'), route('user.product.indexUser'))->add(__($product->name))->getBreadcrumbs(),
            'relatedProducts' => $randomProducts,
        ]);
    }

    public function detailModal($id)
    {
        $product = $this->repository->loadRelations($this->repository->findOrFail($id), [
            'categories:id,name',
            'reviews:rating,content,product_id',
            'productAttributes' => function ($query) {
                return $query->with(['attribute.variations', 'attribute_variations:id']);
            },
            'productVariations.attribute_variations'
        ]);
        $product = new ProductEditResource($product);
        $avg_review_rate = 0;
        $sum_customer_review = count($product->reviews) ? count($product->reviews) : 0;
        foreach ($product->reviews as $review) {
            $avg_review_rate += $review->rating;
        }
        $avg_review_rate = $sum_customer_review != 0 ? $avg_review_rate /= $sum_customer_review : 0;
        return (object) [
            'product' => $product,
            'avgReviewRate' => $avg_review_rate,
            'sumCustomerReview' => $sum_customer_review,
            'is_flash_sale' => true,
        ];
    }

    public function findVariationByAttributeVariationIds(Request $request)
    {
        $id = $request->input('product_id');
        $attributeVariationIds = $request->input('attribute_variation_ids');
        $product = $this->repository->loadRelations($this->repository->findOrFail($id), [
            'productVariations.attribute_variations'
        ]);

        $matchingProductVariation = $product->productVariations->first(function ($productVariation) use ($attributeVariationIds) {
            $variationAttributeIds = $productVariation->attribute_variations->pluck('id')->toArray();
            return empty(array_diff($attributeVariationIds, $variationAttributeIds)) &&
                count($attributeVariationIds) === count($variationAttributeIds);
        });
        if ($matchingProductVariation) {
            return response()->json([
                'status' => true,
                'data' => new ProductVariationResource($matchingProductVariation)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('Không tìm thấy sản phẩm phù hợp.')
            ], 404);
        }
    }

    public function saleLimited()
    {
        $settings = $this->settingRepository->getAll();
        $title = $settings->where('setting_key', 'sale_title')->first()->plain_value;
        $meta_desc = $settings->where('setting_key', 'sale_meta_desc')->first()->plain_value;
        $flashSale = $this->flashSaleRepository->getCurrentFlashSale();

        return view($this->view['sale-limited'], [
            'flashSale' => $flashSale,
            'title' => $title,
            'products' => $flashSale ? $flashSale->details()->paginate(16) : null,
            'meta_desc' => $meta_desc,
            'breadcrumbs' => $this->homeCrums->add(__('Flash Sale'))->getBreadcrumbs(),
        ]);
    }

    public function renderModalProduct($id)
    {
        $product = $this->repository->findOrFail($id);
        return view($this->view['product-modal'], [
            'productModal' => $product,
        ]);
    }

    public function searchProduct(Request $request)
    {
        $data = $request->input('key');
        $products = $this->repository->searchAllLimit($data);
        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }

    public function review(ReviewRequest $request)
    {
        $instance = $this->reviewService->store($request);
        if ($instance) {
            return back()->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }
}
