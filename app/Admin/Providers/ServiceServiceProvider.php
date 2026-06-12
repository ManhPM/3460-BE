<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    protected array $services = [
        'App\Admin\Services\CategorySystem\CategorySystemServiceInterface' => 'App\Admin\Services\CategorySystem\CategorySystemService',
        'App\Admin\Services\Module\ModuleServiceInterface' => 'App\Admin\Services\Module\ModuleService',
        'App\Admin\Services\Permission\PermissionServiceInterface' => 'App\Admin\Services\Permission\PermissionService',
        'App\Admin\Services\Role\RoleServiceInterface' => 'App\Admin\Services\Role\RoleService',
        'App\Admin\Services\Admin\AdminServiceInterface' => 'App\Admin\Services\Admin\AdminService',
        'App\Admin\Services\User\UserServiceInterface' => 'App\Admin\Services\User\UserService',
        'App\Admin\Services\Category\CategoryServiceInterface' => 'App\Admin\Services\Category\CategoryService',
        'App\Admin\Services\Product\ProductServiceInterface' => 'App\Admin\Services\Product\ProductService',
        'App\Admin\Services\Attribute\AttributeServiceInterface' => 'App\Admin\Services\Attribute\AttributeService',
        'App\Admin\Services\AttributeVariation\AttributeVariationServiceInterface' => 'App\Admin\Services\AttributeVariation\AttributeVariationService',
        'App\Admin\Services\Order\OrderServiceInterface' => 'App\Admin\Services\Order\OrderService',
        'App\Admin\Services\Slider\SliderServiceInterface' => 'App\Admin\Services\Slider\SliderService',
        'App\Admin\Services\Slider\SliderItemServiceInterface' => 'App\Admin\Services\Slider\SliderItemService',
        'App\Admin\Services\Post\PostServiceInterface' => 'App\Admin\Services\Post\PostService',
        'App\Admin\Services\PostCategory\PostCategoryServiceInterface' => 'App\Admin\Services\PostCategory\PostCategoryService',
        'App\Admin\Services\Area\AreaServiceInterface' => 'App\Admin\Services\Area\AreaService',
        'App\Admin\Services\Driver\DriverServiceInterface' => 'App\Admin\Services\Driver\DriverService',
        'App\Admin\Services\Store\Category\StoreCategoryServiceInterface' => 'App\Admin\Services\Store\Category\StoreCategoryService',
        'App\Admin\Services\Store\StoreServiceInterface' => 'App\Admin\Services\Store\StoreService',
        'App\Admin\Services\Notification\NotificationServiceInterface' => 'App\Admin\Services\Notification\NotificationService',
        'App\Admin\Services\Topping\ToppingServiceInterface' => 'App\Admin\Services\Topping\ToppingService',
        'App\Admin\Services\Vehicle\VehicleServiceInterface' => 'App\Admin\Services\Vehicle\VehicleService',
        'App\Admin\Services\Discount\DiscountApplicationServiceInterface' => 'App\Admin\Services\Discount\DiscountApplicationService',
        'App\Admin\Services\Discount\DiscountServiceInterface' => 'App\Admin\Services\Discount\DiscountService',
        'App\Admin\Services\FlashSale\FlashSaleServiceInterface' => 'App\Admin\Services\FlashSale\FlashSaleService',

        'App\Admin\Services\Review\ReviewServiceInterface' => 'App\Admin\Services\Review\ReviewService',
        'App\Admin\Services\ShoppingCart\ShoppingCartServiceInterface' => 'App\Admin\Services\ShoppingCart\ShoppingCartService',
        'App\Admin\Services\Bank\BankServiceInterface' => 'App\Admin\Services\Bank\BankService',
        'App\Admin\Services\Section\SectionServiceInterface' => 'App\Admin\Services\Section\SectionService',
        'App\Admin\Services\CommissionWithdrawal\CommissionWithdrawalServiceInterface' => 'App\Admin\Services\CommissionWithdrawal\CommissionWithdrawalService',
        'App\Admin\Services\Voucher\VoucherServiceInterface' => 'App\Admin\Services\Voucher\VoucherService',
        'App\Admin\Services\VoucherProgram\VoucherProgramServiceInterface' => 'App\Admin\Services\VoucherProgram\VoucherProgramService',
        'App\Admin\Services\ShippingRate\ShippingRateServiceInterface' => 'App\Admin\Services\ShippingRate\ShippingRateService',
        'App\Admin\Services\MembershipLevel\MembershipLevelServiceInterface' => 'App\Admin\Services\MembershipLevel\MembershipLevelService',
        'App\Admin\Services\Contact\ContactServiceInterface' => 'App\Admin\Services\Contact\ContactService',
        'App\Admin\Services\WalletTransaction\WalletTransactionServiceInterface' => 'App\Admin\Services\WalletTransaction\WalletTransactionService',

    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
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
