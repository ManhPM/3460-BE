<?php

use Illuminate\Support\Facades\Route;

//lookup
Route::controller(App\Api\V1\Http\Controllers\Home\HomeController::class)
    ->prefix('/lookup')->middleware('optional.jwt')->group(function () {
        Route::get('/home', 'home')->name('home');
        Route::get('/provinces', 'province')->name('province');
        Route::get('/wards', 'ward')->name('ward');
    });

//banks
Route::controller(App\Api\V1\Http\Controllers\Bank\BankController::class)
    ->prefix('/banks')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'list')->name('list');
    });

//membership-levels
Route::controller(App\Api\V1\Http\Controllers\MembershipLevel\MembershipLevelController::class)
    ->prefix('/membership-levels')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

//address
Route::controller(App\Api\V1\Http\Controllers\UserAddress\UserAddressController::class)
    ->middleware('auth:user')
    ->prefix('/addresses')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/show/{id}', 'show')->name('show');
        Route::post('/store', 'store')->name('store');
        Route::put('/update', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        Route::get('/set-default/{id}', 'setDefault')->name('setDefault');
    });

//voucher-program
Route::controller(App\Api\V1\Http\Controllers\VoucherProgram\VoucherProgramController::class)
    ->middleware('auth:user')
    ->prefix('/voucher-programs')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/collect/{id}', 'collect')->name('collect');
    });

//wishlist
Route::controller(App\Api\V1\Http\Controllers\Wishlist\WishlistController::class)
    ->middleware('auth:user')
    ->prefix('/wishlists')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/toggle/{id}', 'toggle')->name('toggle');
    });

//notification
Route::controller(App\Api\V1\Http\Controllers\Notification\NotificationController::class)
    ->middleware('auth:user')
    ->prefix('/notifications')
    ->group(function () {
        Route::post('/update-device-token', 'updateDeviceToken')->name('update_device_token');
        Route::get('/', 'getUserNotifications')->name('getUserNotifications');
        Route::get('/show/{id}', 'detail')->name('detail');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        Route::delete('/delete-all', 'deleteAll')->name('deleteAll');
        Route::get('/read-all', 'updateAllStatusRead')->name('updateAllStatusRead');
    });

//post category
Route::controller(App\Api\V1\Http\Controllers\PostCategory\PostCategoryController::class)
    ->prefix('/posts-categories')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/show/{id}', 'show')->name('show');
    });

//posts
Route::controller(App\Api\V1\Http\Controllers\Post\PostController::class)
    ->prefix('/posts')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/featured', 'featured')->name('featured');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/related/{id}', 'related')->name('related');
    });

Route::middleware('auth:user')->group(function () {
    //order
    Route::controller(App\Api\V1\Http\Controllers\Order\OrderController::class)
        ->prefix('/orders')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/cancel', 'cancel')->name('cancel');
            Route::get('/show/{id}', 'show')->name('show');
        });
});

//shopping cart
Route::controller(App\Api\V1\Http\Controllers\ShoppingCart\ShoppingCartController::class)
    ->prefix('/shopping-cart')
    ->middleware('web', 'optional.jwt')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/checkout', 'checkout')->name('checkout');
        Route::post('/buy-now', 'buyNow')->name('buyNow');
        Route::post('/apply-code', 'applyDiscountCode')->name('applyDiscountCode');
        Route::post('/update', 'update')->name('update');
        Route::post('/change-variation', 'changeVariation')->name('changeVariation');
        Route::post('/delete', 'delete')->name('delete');
    });

Route::prefix('/categories')
    ->group(function () {
        Route::controller(App\Api\V1\Http\Controllers\Category\CategoryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/home', 'home')->name('home');
                Route::get('/product', 'product')->name('product');
                Route::get('/show/{id}', 'show')->name('show');
            });
    });

Route::prefix('/products')
    ->middleware('web', 'optional.jwt')
    ->as('product')
    ->group(function () {
        Route::controller(App\Api\V1\Http\Controllers\Product\ProductController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/flash-sale', 'saleLimited')->name('saleLimited');
                Route::get('/suggested', 'suggested')->name('suggested');
                Route::get('/related/{id}', 'related')->name('related');
                Route::get('/show/{id}', 'show')->name('show');
            });
    });

//review
Route::controller(App\Api\V1\Http\Controllers\Review\ReviewController::class)
    ->prefix('/reviews')
    ->as('review.')
    ->group(function () {
        Route::get('/{productId}', 'index')->name('index');
        Route::post('/', 'store')->middleware('auth:user')->name('store');
    });

//slider
Route::controller(App\Api\V1\Http\Controllers\Slider\SliderController::class)
    ->prefix('/sliders')
    ->group(function () {
        Route::get('/show/{key}', 'show')->name('show');
    });

//auth
Route::controller(App\Api\V1\Http\Controllers\Auth\AuthController::class)
    ->group(function () {
        Route::middleware('auth:user')->prefix('/auth')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/logout', 'logout')->name('logout');
            Route::post('/update', 'update')->name('update');
            Route::post('/withdraw', 'withdraw')->name('withdraw');
            Route::get('/withdraw-history', 'withdrawHistory')->name('withdrawHistory');
            Route::get('/voucher', 'voucher')->name('voucher');
            Route::post('/update-password', 'updatePassword')->name('updatePassword');
        });
        Route::post('/login', 'login')->name('login');
        Route::post('/resend-otp', 'resendOTP')->name('resendOTP');
        Route::post('/verify-phone', 'verifyPhone')->name('verifyPhone');
        Route::post('/delete', 'delete')->name('delete');
        Route::post('/verify-email', 'verifyEmail')->name('verifyEmail');
    })
    ->group(function () {
        Route::middleware('web', 'optional.jwt')->group(function () {
            Route::post('/register', 'register')->name('register');
        });
    });

Route::controller(App\Api\V1\Http\Controllers\Auth\ResetPasswordController::class)
    ->prefix('/reset-password')
    ->group(function () {
        Route::post('/', 'resetPassword')->name('resetPassword');
        Route::post('/verify', 'verify')->name('verify');
        Route::post('/update-password', 'updatePassword')->name('updatePassword');
    });

Route::controller(App\Api\V1\Http\Controllers\Order\OrderController::class)
    ->prefix('/orders')
    ->middleware('auth:user')
    ->group(function () {
        Route::post('/upload-payment-image', 'uploadPaymentImage')->name('uploadPaymentImage');
        Route::get('/bank-transfer-info/{id}', 'getBankTransferInfo')->name('bankTransferInfo');
        Route::put('/update/{id}', 'update')->name('update');
    });

//settings
Route::controller(App\Api\V1\Http\Controllers\Setting\SettingController::class)
    ->prefix('/settings')
    ->as('setting.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

//wallet transactions
Route::controller(App\Api\V1\Http\Controllers\WalletTransaction\WalletTransactionController::class)
    ->middleware('auth:user')
    ->prefix('/wallet-transactions')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/deposit', 'deposit')->name('deposit');
        Route::post('/withdraw', 'withdraw')->name('withdraw');
    });

//affiliate
Route::controller(App\Api\V1\Http\Controllers\Affiliate\AffiliateController::class)
    ->middleware('auth:user')
    ->prefix('/affiliate')
    ->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/recent-transactions', 'recentTransactions')->name('recentTransactions');
        Route::post('/update', 'update')->name('update');
    });

Route::fallback(function () {
    return response()->json([
        'status' => 404,
        'message' => __('Không tìm thấy đường dẫn.')
    ], 404);
});
