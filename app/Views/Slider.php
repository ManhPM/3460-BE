<?php

namespace App\Views;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Slider\SliderRepositoryInterface;
use Illuminate\View\Component;
use App\Admin\Traits\GetConfig;
use App\Enums\Setting\SettingGroup;

class Slider extends Component
{
    use GetConfig;

    protected SliderRepositoryInterface $sliderRepository;
    protected SettingRepositoryInterface $settingRepository;

    public function __construct(SliderRepositoryInterface $sliderRepository, SettingRepositoryInterface $settingRepository)
    {
        $this->sliderRepository = $sliderRepository;
        $this->settingRepository = $settingRepository;
    }

    public function render()
    {
        $slider = $this->sliderRepository->findByField('plain_key', 'home_2');
        $settings = $this->settingRepository->getAll();
        return view('components.slider', compact('slider', 'settings'));
    }
}
