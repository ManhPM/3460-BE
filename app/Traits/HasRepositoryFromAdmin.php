<?php

namespace App\Traits;

use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\ShippingRate\ShippingRateRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;

trait HasRepositoryFromAdmin
{
    protected function getSettingRepository()
    {
        return app(SettingRepositoryInterface::class);
    }

    protected function getShippingRateRepository()
    {
        return app(ShippingRateRepositoryInterface::class);
    }

    protected function getUserRepository()
    {
        return app(UserRepositoryInterface::class);
    }

    protected function getOrderRepository()
    {
        return app(OrderRepositoryInterface::class);
    }

    protected function getPostRepository()
    {
        return app(PostRepositoryInterface::class);
    }

    protected function getMembershipLevelRepository()
    {
        return app(MembershipLevelRepositoryInterface::class);
    }

    protected function getNotificationRepository()
    {
        return app(NotificationRepositoryInterface::class);
    }
}
