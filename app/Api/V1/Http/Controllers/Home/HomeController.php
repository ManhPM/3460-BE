<?php

namespace App\Api\V1\Http\Controllers\Home;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Slider\SliderRepositoryInterface;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Resources\Category\AllCategoryTreeResource;
use App\Api\V1\Http\Resources\Category\AllCategoryTreeWithProductsResource;
use App\Api\V1\Http\Resources\Post\AllPostResource;
use App\Api\V1\Http\Resources\PostCategory\AllPostCategoryTreeResource;
use App\Api\V1\Http\Resources\Product\AllProductBestSellingResource;
use App\Api\V1\Http\Resources\Product\FlashSaleResourceNoPaginate;
use App\Api\V1\Http\Resources\Province\ProvinceResource;
use App\Api\V1\Http\Resources\Slider\SliderResource;
use App\Api\V1\Http\Resources\Ward\WardResource;
use App\Api\V1\Repositories\Category\CategoryRepositoryInterface;
use App\Api\V1\Repositories\Post\PostRepositoryInterface;
use App\Api\V1\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Api\V1\Support\Response;
use Illuminate\Http\JsonResponse;

/**
 * @group DS Bổ sung
 */

class HomeController extends Controller
{
    use Response, AuthService;

    protected $settingRepository;
    protected $bankRepository;
    protected $sliderRepository;
    protected $productRepository;
    protected $postRepository;
    protected $postCategoryRepository;
    protected $categoryRepository;
    protected $flashSaleRepository;
    protected $provinceRepository;
    protected $wardRepository;
    protected $membershipLevelRepository;

    public function __construct(
        SettingRepositoryInterface $settingRepository,
        BankRepositoryInterface $bankRepository,
        SliderRepositoryInterface $sliderRepository,
        ProductRepositoryInterface $productRepository,
        PostRepositoryInterface $postRepository,
        PostCategoryRepositoryInterface $postCategoryRepository,
        CategoryRepositoryInterface $categoryRepository,
        FlashSaleRepositoryInterface $flashSaleRepository,
        ProvinceRepositoryInterface $provinceRepository,
        WardRepositoryInterface $wardRepository,
        MembershipLevelRepositoryInterface $membershipLevelRepository,
    ) {
        $this->settingRepository = $settingRepository;
        $this->bankRepository = $bankRepository;
        $this->sliderRepository = $sliderRepository;
        $this->productRepository = $productRepository;
        $this->postRepository = $postRepository;
        $this->postCategoryRepository = $postCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->flashSaleRepository = $flashSaleRepository;
        $this->provinceRepository = $provinceRepository;
        $this->wardRepository = $wardRepository;
        $this->membershipLevelRepository = $membershipLevelRepository;
    }

    public function home(): JsonResponse
    {

        $banner = $this->sliderRepository->findByField('plain_key', 'banner')->load('items');
        $banner2 = $this->sliderRepository->findByField('plain_key', 'banner_2')->load('items');
        $popupImageSlider = $this->sliderRepository->findByField('plain_key', 'popup_image')->load('items');
        $randomPopup = optional($popupImageSlider->items)->random();
        $posts = $this->postRepository->getFeaturedPaginate();
        $bestSellingProducts = $this->productRepository->getBestSellingProducts(8);
        $flashSale = $this->flashSaleRepository->getCurrentFlashSale();
        $parentCategories = $this->categoryRepository->getHomeParentCategories();
        $homeCategories = $this->categoryRepository->getMobileHomeCategories();
        $homePostCategories = new AllPostCategoryTreeResource($this->postCategoryRepository->getTreeHome());

        return $this->jsonResponseSuccess([
            'banner' => new SliderResource($banner),
            'banner2' => new SliderResource($banner2),
            'categories' => new AllCategoryTreeResource($parentCategories),
            'image_popup' => asset($randomPopup->avatar),
            'flash_sale' => $flashSale ? new FlashSaleResourceNoPaginate($flashSale) : null,
            'best_selling_products' => new AllProductBestSellingResource($bestSellingProducts),
            'posts' => new AllPostResource($posts),
            'home_categories' => new AllCategoryTreeWithProductsResource($homeCategories),
            'home_post_categories' => $homePostCategories
        ]);
    }

    public function province(): JsonResponse
    {
        $provinces = $this->provinceRepository->getQueryBuilder()->get();
        return $this->jsonResponseSuccess(new ProvinceResource($provinces));
    }

    public function ward(): JsonResponse
    {
        $wards = $this->wardRepository->getQueryBuilder()->get();
        return $this->jsonResponseSuccess(new WardResource($wards));
    }
}
