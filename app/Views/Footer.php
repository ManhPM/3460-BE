<?php

namespace App\Views;

use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\View\Component;
use App\Admin\Traits\GetConfig;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Slider\SliderRepositoryInterface;
use App\Enums\Setting\SettingGroup;

class Footer extends Component
{
    use GetConfig;

    protected CategoryRepositoryInterface $categoryRepository;
    protected SettingRepositoryInterface $settingRepository;
    protected SliderRepositoryInterface $sliderRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SettingRepositoryInterface $settingRepository,
        SliderRepositoryInterface $sliderRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->settingRepository = $settingRepository;
        $this->sliderRepository = $sliderRepository;
    }

    public function render()
    {
        $parentCategories = $this->categoryRepository->getParentCategory();
        $settingsFooter = $this->settingRepository->getByGroup([SettingGroup::Footer]);
        $popupImageSlider = $this->sliderRepository->findByField('plain_key', 'popup_image')->load('items');
        $randomPopup = optional($popupImageSlider->items)->random();
        return view('components.layouts.footer', compact('parentCategories', 'settingsFooter', 'randomPopup'));
    }
}
