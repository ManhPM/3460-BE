<?php

namespace App\Views;

use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use Illuminate\View\Component;
use App\Admin\Traits\GetConfig;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Enums\Setting\SettingGroup;

class Header extends Component
{
    use GetConfig;

    protected CategoryRepositoryInterface $categoryRepository;

    protected SettingRepositoryInterface $settingRepository;
    protected FlashSaleRepositoryInterface $flashSaleRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SettingRepositoryInterface $settingRepository,
        FlashSaleRepositoryInterface $flashSaleRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->settingRepository = $settingRepository;
        $this->flashSaleRepository = $flashSaleRepository;
    }

    public function getCategoriesWithChildren($categories)
    {
        foreach ($categories as $category) {
            if (!$category->relationLoaded('children')) {
                $category->load('children');
            }
            $this->getCategoriesWithChildren($category->children);
        }

        return $categories;
    }

    public function render()
    {
        $categories = $this->categoryRepository->getFlatTree();
        $parentTempCategories = $this->categoryRepository->getParentCategory();
        $parentCategories = $this->getCategoriesWithChildren($parentTempCategories);
        $settings = $this->settingRepository->getAll();
        $flashSale = $this->flashSaleRepository->getCurrentFlashSale();
        return view('components.layouts.header', compact('categories', 'parentCategories', 'settings', 'flashSale'));
    }
}
