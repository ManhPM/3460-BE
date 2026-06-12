<?php

namespace App\Admin\Http\Controllers\Setting;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Support\Breadcrumb\Breadcrumb;
use App\Enums\Setting\SettingGroup;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected Breadcrumb $crums;
    public function __construct(
        SettingRepositoryInterface $repository
    ) {
        parent::__construct();
        $this->crums = (new Breadcrumb())->add(__('Dashboard'), route('admin.dashboard'));
        $this->repository = $repository;
    }
    public function getView()
    {
        return [
            'general' => 'admin.settings.general',
            'footer' => 'admin.settings.footer',
            'contact' => 'admin.settings.contact',
            'intro' => 'admin.settings.intro',
            'user_shopping' => 'admin.settings.user-shopping',
        ];
    }

    public function general()
    {
        $breadcrumbs = $this->crums->add(__('Cài đặt chung'));
        $settings = $this->repository->getByGroup([SettingGroup::General]);
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function theme()
    {
        $settings = $this->repository->getByGroup([SettingGroup::CMSTheme]);
        $breadcrumbs = $this->crums->add(__('Cài đặt Theme'));
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function mobile()
    {
        $settings = $this->repository->getByGroup([SettingGroup::MobileTheme]);
        $breadcrumbs = $this->crums->add(__('Cài đặt App'));
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function footer()
    {
        $breadcrumbs = $this->crums->add(__('Cài đặt footer'));
        $settings = $this->repository->getByGroup([SettingGroup::Footer]);
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function contact()
    {
        $breadcrumbs = $this->crums->add(__('Cài đặt trang liên hệ'));
        $settings = $this->repository->getByGroup([SettingGroup::Contact]);
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function information()
    {
        $breadcrumbs = $this->crums->add(__('Cài đặt trang giới thiệu'));
        $settings = $this->repository->getByGroup([SettingGroup::Information]);
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function webTheme()
    {
        $settings = $this->repository->getByGroup([SettingGroup::WebTheme]);
        $breadcrumbs = $this->crums->add(__('Cài đặt Theme cho trang web bán hàng'));
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function config()
    {
        $settings = $this->repository->getByGroup([SettingGroup::Config]);
        $breadcrumbs = $this->crums->add(__('Cấu hình'));
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function membershipLevel()
    {
        $settings = $this->repository->getByGroup([SettingGroup::Membership]);
        $breadcrumbs = $this->crums->add(__('Cấu hình hạng thành viên'));
        return view($this->view['general'], compact('settings', 'breadcrumbs'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');
        $this->repository->updateMultipleRecord($data);
        return back()->with('success', __('success'));
    }
}
