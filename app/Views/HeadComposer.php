<?php

namespace App\Views;

use App\Admin\Traits\GetConfig;
use Illuminate\View\View;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Enums\Setting\SettingGroup;

class HeadComposer
{
    use GetConfig;
    protected SettingRepositoryInterface $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function compose(View $view)
    {
        $settings = $this->settingRepository->getAll();
        $view->with('settings', $settings);
    }
}
