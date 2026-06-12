<?php

namespace App\Api\AdminV1\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    protected $services = [
        'App\Api\AdminV1\Services\Product\ProductService' => 'App\Api\AdminV1\Services\Product\ProductService',
        'App\Api\AdminV1\Services\Category\CategoryService' => 'App\Api\AdminV1\Services\Category\CategoryService',
        'App\Api\AdminV1\Services\Attribute\AttributeService' => 'App\Api\AdminV1\Services\Attribute\AttributeService',
        'App\Api\AdminV1\Services\Order\OrderService' => 'App\Api\AdminV1\Services\Order\OrderService',
        'App\Api\AdminV1\Services\User\UserService' => 'App\Api\AdminV1\Services\User\UserService',
        'App\Api\AdminV1\Services\Inventory\InventoryService' => 'App\Api\AdminV1\Services\Inventory\InventoryService',
        'App\Api\AdminV1\Services\Review\ReviewService' => 'App\Api\AdminV1\Services\Review\ReviewService',
        'App\Api\AdminV1\Services\Discount\DiscountService' => 'App\Api\AdminV1\Services\Discount\DiscountService',
        'App\Api\AdminV1\Services\Voucher\VoucherService' => 'App\Api\AdminV1\Services\Voucher\VoucherService',
        'App\Api\AdminV1\Services\Setting\SettingService' => 'App\Api\AdminV1\Services\Setting\SettingService',
        'App\Api\AdminV1\Services\Admin\AdminService' => 'App\Api\AdminV1\Services\Admin\AdminService',
        'App\Api\AdminV1\Services\Role\RoleService' => 'App\Api\AdminV1\Services\Role\RoleService',
        'App\Api\AdminV1\Services\FlashSale\FlashSaleService' => 'App\Api\AdminV1\Services\FlashSale\FlashSaleService',
        'App\Api\AdminV1\Services\Post\PostService' => 'App\Api\AdminV1\Services\Post\PostService',
        'App\Api\AdminV1\Services\PostCategory\PostCategoryService' => 'App\Api\AdminV1\Services\PostCategory\PostCategoryService',
        'App\Api\AdminV1\Services\Slider\SliderService' => 'App\Api\AdminV1\Services\Slider\SliderService',
        'App\Api\AdminV1\Services\Notification\NotificationService' => 'App\Api\AdminV1\Services\Notification\NotificationService',
        'App\Api\AdminV1\Services\ShippingRate\ShippingRateService' => 'App\Api\AdminV1\Services\ShippingRate\ShippingRateService',
        'App\Api\AdminV1\Services\Bank\BankService' => 'App\Api\AdminV1\Services\Bank\BankService',
        'App\Api\AdminV1\Services\MembershipLevel\MembershipLevelService' => 'App\Api\AdminV1\Services\MembershipLevel\MembershipLevelService',
        'App\Api\AdminV1\Services\VoucherProgram\VoucherProgramService' => 'App\Api\AdminV1\Services\VoucherProgram\VoucherProgramService',
        'App\Api\AdminV1\Services\CommissionWithdrawal\CommissionWithdrawalService' => 'App\Api\AdminV1\Services\CommissionWithdrawal\CommissionWithdrawalService',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->services as $interface => $implement) {
            $this->app->singleton($interface, $implement);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

