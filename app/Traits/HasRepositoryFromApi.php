<?php

namespace App\Traits;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\ShippingRate\ShippingRateRepositoryInterface;
use App\Api\V1\Repositories\Order\OrderRepositoryInterface;
use App\Api\V1\Repositories\Post\PostRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;

trait HasRepositoryFromApi
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
}
