<?php

namespace App\Api\AdminV1\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $repositories = [
        'App\Api\AdminV1\Repositories\Product\ProductRepositoryInterface' => 'App\Api\AdminV1\Repositories\Product\ProductRepository',
        'App\Api\AdminV1\Repositories\Category\CategoryRepositoryInterface' => 'App\Api\AdminV1\Repositories\Category\CategoryRepository',
        'App\Api\AdminV1\Repositories\Attribute\AttributeRepositoryInterface' => 'App\Api\AdminV1\Repositories\Attribute\AttributeRepository',
        'App\Api\AdminV1\Repositories\Order\OrderRepositoryInterface' => 'App\Api\AdminV1\Repositories\Order\OrderRepository',
        'App\Api\AdminV1\Repositories\User\UserRepositoryInterface' => 'App\Api\AdminV1\Repositories\User\UserRepository',
        'App\Api\AdminV1\Repositories\Inventory\InventoryRepositoryInterface' => 'App\Api\AdminV1\Repositories\Inventory\InventoryRepository',
        'App\Api\AdminV1\Repositories\Review\ReviewRepositoryInterface' => 'App\Api\AdminV1\Repositories\Review\ReviewRepository',
        'App\Api\AdminV1\Repositories\Discount\DiscountRepositoryInterface' => 'App\Api\AdminV1\Repositories\Discount\DiscountRepository',
        'App\Api\AdminV1\Repositories\Voucher\VoucherRepositoryInterface' => 'App\Api\AdminV1\Repositories\Voucher\VoucherRepository',
        'App\Api\AdminV1\Repositories\Setting\SettingRepositoryInterface' => 'App\Api\AdminV1\Repositories\Setting\SettingRepository',
        'App\Api\AdminV1\Repositories\Admin\AdminRepositoryInterface' => 'App\Api\AdminV1\Repositories\Admin\AdminRepository',
        'App\Api\AdminV1\Repositories\Role\RoleRepositoryInterface' => 'App\Api\AdminV1\Repositories\Role\RoleRepository',
        'App\Api\AdminV1\Repositories\Permission\PermissionRepositoryInterface' => 'App\Api\AdminV1\Repositories\Permission\PermissionRepository',
        'App\Api\AdminV1\Repositories\FlashSale\FlashSaleRepositoryInterface' => 'App\Api\AdminV1\Repositories\FlashSale\FlashSaleRepository',
        'App\Api\AdminV1\Repositories\Post\PostRepositoryInterface' => 'App\Api\AdminV1\Repositories\Post\PostRepository',
        'App\Api\AdminV1\Repositories\PostCategory\PostCategoryRepositoryInterface' => 'App\Api\AdminV1\Repositories\PostCategory\PostCategoryRepository',
        'App\Api\AdminV1\Repositories\Slider\SliderRepositoryInterface' => 'App\Api\AdminV1\Repositories\Slider\SliderRepository',
        'App\Api\AdminV1\Repositories\Notification\NotificationRepositoryInterface' => 'App\Api\AdminV1\Repositories\Notification\NotificationRepository',
        'App\Api\AdminV1\Repositories\ShippingRate\ShippingRateRepositoryInterface' => 'App\Api\AdminV1\Repositories\ShippingRate\ShippingRateRepository',
        'App\Api\AdminV1\Repositories\Bank\BankRepositoryInterface' => 'App\Api\AdminV1\Repositories\Bank\BankRepository',
        'App\Api\AdminV1\Repositories\MembershipLevel\MembershipLevelRepositoryInterface' => 'App\Api\AdminV1\Repositories\MembershipLevel\MembershipLevelRepository',
        'App\Api\AdminV1\Repositories\VoucherProgram\VoucherProgramRepositoryInterface' => 'App\Api\AdminV1\Repositories\VoucherProgram\VoucherProgramRepository',
        'App\Api\AdminV1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface' => 'App\Api\AdminV1\Repositories\WalletTransaction\WalletTransactionRepository',
        'App\Api\AdminV1\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface' => 'App\Api\AdminV1\Repositories\CommissionWithdrawal\CommissionWithdrawalRepository',
        'App\Api\AdminV1\Repositories\Dashboard\DashboardRepositoryInterface' => 'App\Api\AdminV1\Repositories\Dashboard\DashboardRepository',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->repositories as $interface => $implement) {
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
