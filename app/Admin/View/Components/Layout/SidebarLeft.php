<?php

namespace App\Admin\View\Components\Layout;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use Illuminate\View\Component;
use App\Admin\Traits\GetConfig;

class SidebarLeft extends Component
{
    use GetConfig;
    public $menu;
    protected SettingRepositoryInterface $settingRepository;
    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
        $this->menu = $this->traitGetConfigSidebar();
    }

    public function routeName($routeName, $param)
    {
        return $routeName ? route($routeName, $param) : '#';
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $settings = $this->settingRepository->getAll();
        $permissions = auth('admin')->user()->getAllPermissions()->pluck('name');
        return view('admin.layouts.sidebar-left', compact('settings'));
    }
}
