<?php

use Illuminate\Support\Facades\Route;
use App\Api\AdminV1\Http\Controllers\Auth\AuthController;
use App\Api\AdminV1\Http\Controllers\Product\ProductController;
use App\Api\AdminV1\Http\Controllers\Category\CategoryController;
use App\Api\AdminV1\Http\Controllers\Order\OrderController;
use App\Api\AdminV1\Http\Controllers\User\UserController;
use App\Api\AdminV1\Http\Controllers\Inventory\InventoryController;
use App\Api\AdminV1\Http\Controllers\Review\ReviewController;
use App\Api\AdminV1\Http\Controllers\Discount\DiscountController;
use App\Api\AdminV1\Http\Controllers\Voucher\VoucherController;
use App\Api\AdminV1\Http\Controllers\Setting\SettingController;
use App\Api\AdminV1\Http\Controllers\Admin\AdminController;
use App\Api\AdminV1\Http\Controllers\Role\RoleController;
use App\Api\AdminV1\Http\Controllers\Permission\PermissionController;
use App\Api\AdminV1\Http\Controllers\FlashSale\FlashSaleController;
use App\Api\AdminV1\Http\Controllers\Post\PostController;
use App\Api\AdminV1\Http\Controllers\PostCategory\PostCategoryController;
use App\Api\AdminV1\Http\Controllers\Slider\SliderController;
use App\Api\AdminV1\Http\Controllers\Slider\SliderItemController;
use App\Api\AdminV1\Http\Controllers\Notification\NotificationController;
use App\Api\AdminV1\Http\Controllers\ShippingRate\ShippingRateController;
use App\Api\AdminV1\Http\Controllers\Bank\BankController;
use App\Api\AdminV1\Http\Controllers\MembershipLevel\MembershipLevelController;
use App\Api\AdminV1\Http\Controllers\VoucherProgram\VoucherProgramController;
use App\Api\AdminV1\Http\Controllers\WalletTransaction\WalletTransactionController;
use App\Api\AdminV1\Http\Controllers\CommissionWithdrawal\CommissionWithdrawalController;
use App\Api\AdminV1\Http\Controllers\Attribute\AttributeController;
use App\Api\AdminV1\Http\Controllers\Sidebar\SidebarController;
use App\Api\AdminV1\Http\Controllers\Dashboard\DashboardController;
use App\Api\AdminV1\Http\Controllers\Lookup\LookupController;
use App\Api\AdminV1\Http\Controllers\File\FileController;

/*
|--------------------------------------------------------------------------
| Admin API Routes v1
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:admin-api');
    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:admin-api');
    Route::post('profile', [AuthController::class, 'updateProfile'])->middleware('auth:admin-api');
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:admin-api');
});

// Protected Routes
Route::middleware('auth:admin-api')->group(function () {

    // Products
    Route::apiResource('products', ProductController::class);
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate']);
    Route::put('products/{product}/attributes', [ProductController::class, 'updateAttributes']);
    Route::put('products/{product}/variations', [ProductController::class, 'updateVariations']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Attributes
    Route::apiResource('attributes', AttributeController::class);
    Route::get('attributes/{attribute}/variations', [\App\Api\AdminV1\Http\Controllers\AttributeVariation\AttributeVariationController::class, 'index']);
    Route::apiResource('attribute-variations', \App\Api\AdminV1\Http\Controllers\AttributeVariation\AttributeVariationController::class)->except(['index']);

    // Orders
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{order}/confirm', [OrderController::class, 'confirm']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus']);

    // Users (Customers)
    Route::apiResource('users', UserController::class);
    Route::get('users/search/list', [UserController::class, 'search']);
    Route::get('users/{user}/orders', [UserController::class, 'orders']);
    Route::get('users/{user}/addresses', [UserController::class, 'addresses']);
    Route::get('users/{user}/point-earned-history', [UserController::class, 'pointEarnedHistory']);
    Route::get('users/{user}/point-used-history', [UserController::class, 'pointUsedHistory']);
    Route::get('users/{user}/wallet-transactions', [UserController::class, 'walletTransactions']);

    // Inventory
    Route::get('inventories', [InventoryController::class, 'index']);
    Route::get('inventories/data', [InventoryController::class, 'getData']);
    Route::post('inventories/update-qty', [InventoryController::class, 'updateQuantity']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show', 'destroy']);
    Route::post('reviews/{review}/reply', [ReviewController::class, 'reply']);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve']);
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject']);

    // Discounts
    Route::apiResource('discounts', DiscountController::class);

    // Vouchers
    Route::apiResource('vouchers', VoucherController::class);
    Route::post('vouchers/{voucher}/toggle-status', [VoucherController::class, 'toggleStatus']);

    // Settings
    Route::get('settings', [SettingController::class, 'index']);
    Route::put('settings', [SettingController::class, 'update']);

    // Admins
    Route::apiResource('admins', AdminController::class);

    // Roles
    Route::apiResource('roles', RoleController::class);

    // Permissions
    Route::get('permissions', [PermissionController::class, 'index']);

    // Flash Sales
    Route::apiResource('flash-sales', FlashSaleController::class);

    // Posts
    Route::apiResource('posts', PostController::class);

    // Post Categories
    Route::apiResource('post-categories', PostCategoryController::class);

    // Sliders
    Route::apiResource('sliders', SliderController::class);
    Route::get('sliders/{slider}/items', [\App\Api\AdminV1\Http\Controllers\Slider\SliderItemController::class, 'index']);
    Route::post('sliders/{slider}/items', [\App\Api\AdminV1\Http\Controllers\Slider\SliderItemController::class, 'store']);
    Route::put('slider-items/{item}', [\App\Api\AdminV1\Http\Controllers\Slider\SliderItemController::class, 'update']);
    Route::delete('slider-items/{item}', [\App\Api\AdminV1\Http\Controllers\Slider\SliderItemController::class, 'destroy']);

    // Notifications
    Route::apiResource('notifications', NotificationController::class);

    // Shipping Rates
    Route::apiResource('shipping-rates', ShippingRateController::class);

    // Banks
    Route::apiResource('banks', BankController::class);
    Route::get('banks/list/unique', [BankController::class, 'listUnique']);

    // Membership Levels
    Route::apiResource('membership-levels', MembershipLevelController::class);

    // Voucher Programs
    Route::apiResource('voucher-programs', VoucherProgramController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::post('voucher-programs/give-voucher', [VoucherProgramController::class, 'giveVoucher']);
    Route::post('voucher-programs/reset', [VoucherProgramController::class, 'reset']);
    Route::post('voucher-programs/{id}/toggle-status', [VoucherProgramController::class, 'toggleStatus']);

    // Wallet Transactions
    Route::apiResource('wallet-transactions', WalletTransactionController::class);
    Route::post('wallet-transactions/{wallet_transaction}/approve', [WalletTransactionController::class, 'approve']);
    Route::post('wallet-transactions/{wallet_transaction}/reject', [WalletTransactionController::class, 'reject']);

    // Commission Withdrawals
    Route::apiResource('commission-withdrawals', CommissionWithdrawalController::class)->only(['index', 'show']);
    Route::post('commission-withdrawals/{commissionWithdrawal}/update-status', [CommissionWithdrawalController::class, 'updateStatus']);

    // Sidebar
    Route::get('sidebar/permissions', [SidebarController::class, 'permissions']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Lookup
    Route::get('lookup/provinces', [LookupController::class, 'provinces']);
    Route::get('lookup/wards', [LookupController::class, 'wards']);

    // Files
    Route::post('files/upload', [FileController::class, 'upload']);
    Route::get('files/list', [FileController::class, 'list']);
});
