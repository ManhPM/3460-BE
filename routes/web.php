<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;



Route::get('/checkout', function () {
    return view('checkout');
});

Route::controller(App\Http\Controllers\Stripe\StripeController::class)
    ->prefix('/')
    ->group(function () {
        Route::post('/create-checkout-session', 'createCheckoutSession')->name('createCheckoutSession');
        Route::get('/checkout-success', 'handleCheckoutSuccess')->name('handleCheckoutSuccess');
        Route::get('/checkout-cancel', 'handleCheckoutCancel')->name('handleCheckoutCancel');
    });


Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return "Cache cleared!";
});

Route::group(['middleware' => 'admin.auth.user:web'], function () {
    Route::controller(App\Http\Controllers\Contact\ContactController::class)
        ->prefix('/don-lien-he')
        ->as('contact-user.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('/delete{id}', 'delete')->name('delete');
            Route::post('/register', 'register')->name('register');
            Route::post('/cancel', 'cancel')->name('cancel');
            Route::post('/update', 'update')->name('update');
        });
});


Route::controller(App\Http\Controllers\Home\UserHomeController::class)
    ->prefix('/')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/gioi-thieu', 'information')->name('information');
        Route::get('/lien-he', 'contact')->name('contact');
        Route::get('/tra-cuu', 'getOrderDetailForCustomer')->name('getOrderDetailForCustomer');
        Route::post('/xac-thuc-thanh-toan', 'uploadCheckoutImage')->name('uploadCheckoutImage');
        Route::get('/chinh-sach-bao-mat', 'privacyPolicy')->name('privacyPolicy');
        Route::get('/quy-che-hoat-dong', 'operatingRegulations')->name('operatingRegulations');
        Route::get('/chinh-sach-van-chuyen', 'shippingPolicy')->name('shippingPolicy');
        Route::get('/chinh-sach-tra-hang-va-hoan-tien', 'returnAndRefundPolicy')->name('returnAndRefundPolicy');
    });

Route::controller(App\Http\Controllers\Home\UserHomeController::class)
    ->prefix('/vnpay-payment')
    ->as('payment.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

Route::controller(App\Http\Controllers\Product\ProductController::class)
    ->prefix('/san-pham')
    ->as('product.')
    ->group(function () {
        Route::get('/', 'indexUser')->name('indexUser');
        Route::get('/khuyen-mai-gioi-han', 'saleLimited')->name('saleLimited');
        Route::get('/{slug?}', 'detail')->name('detail');
        Route::get('/render-modal/{id?}', 'renderModalProduct')->name('render');
        Route::get('/detailModal/{id}', 'detailModal')->name('detailModal');
        Route::get('/find/find-variation-by-attribute-ids', 'findVariationByAttributeVariationIds')->name('findVariationByAttributeVariationIds');
        Route::get('/filter/all', 'searchProduct')->name('search');
        Route::post('/{slug}/danh-gia', 'review')->name('review');
    });

Route::controller(App\Http\Controllers\ShoppingCart\ShoppingCartController::class)
    ->prefix('/gio-hang')
    ->as('cart.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/load-cart', 'getCartItems')->name('items');
        Route::post('/', 'store')->name('store');
        Route::put('/', 'update')->name('update');
        Route::post('/apply', 'applyDiscountCode')->name('applyCode');
        Route::post('/apply-voucher', 'applyVoucher')->name('applyVoucher');
        Route::post('/increament', 'increament')->name('increament');
        Route::post('/decreament', 'decreament')->name('decreament');
        Route::delete('/remove/{id?}', 'delete')->name('remove');
        Route::post('/buy-now', 'buyNow')->name('buyNow');
        Route::get('/thanh-toan', 'checkout')->name('checkout');
        Route::get('/khoi-tao-vnpay/{code}', 'prepareDataVnpay')->name('prepareDataVnpay');
        Route::get('/vnpay', 'handleVnpay')->name('handleVnpay');
        Route::get('/vnpay/return', 'handleVnpayReturn')->name('handleVnpayReturn');
        Route::post('/checkout-final', 'checkoutFinal')->name('checkoutFinal');
    });

Route::controller(App\Http\Controllers\Notification\NotificationController::class)
    ->middleware('admin.auth.user:web')
    ->prefix('/thong-bao')
    ->as('notification.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/chi-tiet/{id}', 'show')->name('show');
        Route::get('/doc-tat-ca', 'readAll')->name('readAll');
    });

Route::controller(App\Http\Controllers\MembershipLevel\MembershipLevelController::class)
    ->middleware('admin.auth.user:web')
    ->prefix('/thanh-vien')
    ->as('membership_level.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });


Route::controller(App\Http\Controllers\VoucherProgram\VoucherProgramController::class)
    ->middleware('admin.auth.user:web')
    ->prefix('/thu-thap-voucher')
    ->as('voucher_program.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/collect', 'collect')->name('collect');
    });

Route::controller(App\Http\Controllers\Auth\LoginController::class)
    ->prefix('/auth')
    ->as('auth.')
    ->group(function () {
        Route::get('/', 'indexUser')->name('indexUser')->middleware('guest.web:web');
        Route::get('/forgot-password', 'forgotPassword')->name('forgotPassword')->middleware('guest.web:web');
        Route::post('/forgot-password', 'forgotPasswordSend')->name('forgotPasswordSend');
        Route::get('/reset-password', 'resetPassword')->name('resetPassword');
        Route::put('/reset-password', 'changePassword')->name('changePassword');
        Route::post('/', 'loginUser')->name('loginUser');
        Route::post('/register', 'register')->name('register');
        Route::post('/resend-otp', 'resendOTP')->name('resendOTP');
        Route::get('/oauth-verification', 'oauth')->name('oauth')->middleware('guest.web:web');
        Route::post('/oauth-verification', 'verify')->name('verify');
    });

Route::controller(App\Http\Controllers\Auth\ResetPasswordController::class)
    ->prefix('/reset-password')
    ->as('password.reset.')
    ->group(function () {
        Route::post('/edit', 'edit')->name('edit');
        Route::get('/verify', 'verify')->name('verify');
        Route::put('/update', 'update')->name('update');
    });

Route::controller(App\Http\Controllers\Auth\ResetPasswordApiController::class)
    ->prefix('/reset-password-api')
    ->as('password.reset.api.')
    ->group(function () {
        Route::get('/edit', 'edit')->name('edit')->middleware('signed');
        Route::put('/update', 'update')->name('update');
        Route::get('/success', 'success')->name('success');
    });

Route::controller(App\Http\Controllers\Auth\ActiveAccountController::class)
    ->prefix('/activate-account')
    ->as('activation')
    ->group(function () {
        Route::get('/', 'index')->name('index')->middleware('signed');
    });
Route::group(['middleware' => 'admin.auth.user:web'], function () {
    Route::controller(App\Http\Controllers\Order\OrderController::class)
        ->prefix('/don-hang')
        ->as('order.')
        ->group(function () {
            Route::get('/', 'indexUser')->name('indexUser');
            Route::get('/thong-ke', 'statistical')->name('statistical');
            if (env('IS_PRO')) {
                Route::get('/tiep-thi-lien-ket', 'affiliate')->name('affiliate');
            }
            Route::get('/chi-tiet/{id}', 'detail')->name('detail');
            Route::post('/huy', 'cancel')->name('cancel');
            Route::post('/hoan-don', 'return')->name('return');
            Route::get('/danh-gia/{id?}', 'createReview')->name('createReview');
            Route::get('/chi-tiet-danh-gia/{productId?}/{userId?}', 'showReview')->name('showReview');
            Route::post('/them-danh-gia', 'storeReview')->name('storeReview');
            Route::put('/sua-danh-gia', 'updateReview')->name('updateReview');
            Route::delete('/xoa-danh-gia/{id?}', 'deleteReview')->name('deleteReview');
        });
});

Route::group(['middleware' => 'admin.auth.user:web'], function () {
    Route::controller(App\Http\Controllers\Order\OrderController::class)
        ->prefix('/don-hang')
        ->as('order.')
        ->group(function () {
            Route::get('/', 'indexUser')->name('indexUser');
            Route::get('/gioi-thieu', 'affiliate')->name('affiliate');
            Route::get('/chi-tiet/{id}', 'detail')->name('detail');
            Route::get('/huy/{id?}', 'cancel')->name('cancel');
            Route::get('/danh-gia/{id?}', 'createReview')->name('createReview');
            Route::get('/chi-tiet-danh-gia/{productId?}/{userId?}', 'showReview')->name('showReview');
            Route::post('/them-danh-gia', 'storeReview')->name('storeReview');
            Route::put('/sua-danh-gia', 'updateReview')->name('updateReview');
            Route::delete('/xoa-danh-gia/{id?}', 'deleteReview')->name('deleteReview');
        });
});


Route::group(['middleware' => 'admin.auth.user:web'], function () {
    Route::controller(App\Http\Controllers\UserAddress\UserAddressController::class)
        ->prefix('/dia-chi')
        ->as('address.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::put('/update', 'update')->name('update');
            Route::get('/delete/{id?}', 'delete')->name('delete');
            Route::get('/set-default/{id?}', 'setDefaultAddress')->name('setDefault');
        });
    Route::controller(App\Http\Controllers\UserAddress\UserAddressController::class)
        ->prefix('/api/dia-chi')
        ->as('address.api.')
        ->group(function () {
            Route::get('/', 'indexApi')->name('index');
            Route::post('/store', 'storeApi')->name('store');
            Route::put('/update', 'updateApi')->name('update');
            Route::get('/delete/{id?}', 'deleteApi')->name('delete');
            Route::get('/set-default/{id?}', 'setDefaultAddressApi')->name('setDefault');
        });
});

if (env('IS_PRO')) {
    Route::group(['middleware' => 'admin.auth.user:web'], function () {
        Route::controller(App\Http\Controllers\ShoppingCart\ShoppingCartController::class)
            ->prefix('/voucher')
            ->as('voucher.')
            ->group(function () {
                Route::get('/', 'voucher')->name('index');
                Route::get('/{id?}', 'detailVoucher')->name('detail');
            });
    });
}

Route::group(['middleware' => 'admin.auth.user:web'], function () {
    Route::controller(App\Http\Controllers\Wishlist\WishlistController::class)
        ->prefix('/danh-sach-yeu-thich')
        ->as('wishlist.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/toggle/{id?}', 'toggle')->name('toggle');
            Route::get('/xoa/{id?}', 'delete')->name('delete');
        });
});

if (env('IS_PRO')) {
    Route::group(['middleware' => 'admin.auth.user:web'], function () {
        Route::controller(App\Admin\Http\Controllers\CommissionWithdrawal\CommissionWithdrawalController::class)
            ->prefix('/giao-dich')
            ->as('commission_withdrawal.')
            ->group(function () {
                Route::get('/', 'indexUser')->name('indexUser');
                Route::post('/them', 'store')->name('store');
            });
    });
}

Route::controller(App\Admin\Http\Controllers\Auth\ChangePasswordController::class)
    ->prefix('/password')
    ->as('password.')
    ->group(function () {
        Route::get('/', 'indexUser')->name('indexUser');
        Route::put('/', 'update')->name('update');
    });

Route::group(['middleware' => 'admin.auth.user:web'], function () {
    Route::controller(App\Admin\Http\Controllers\Auth\ProfileController::class)
        ->prefix('/tai-khoan')
        ->as('profile.')
        ->group(function () {
            Route::get('/', 'indexUser')->name('indexUser');
            Route::put('/', 'update')->name('update');
        });
    Route::get('/logout', [App\Admin\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
});

Route::controller(App\Http\Controllers\Auth\ResetPasswordController::class)
    ->prefix('/reset-password')
    ->as('password.reset.')
    ->group(function () {
        Route::get('/edit', 'edit')->name('edit')->middleware('signed');
        Route::put('/update', 'update')->name('update');
        Route::get('/success', 'success')->name('success');
    });
// Product redirect route - để mở app từ web
Route::controller(App\Api\V1\Http\Controllers\Product\ProductController::class)
    ->prefix('/product')
    ->group(function () {
        Route::get('/{id}', 'redirect')->name('product.redirect');
    });

Route::controller(App\Http\Controllers\Post\PostController::class)
    ->as('post.')
    ->group(function () {
        Route::get('/tin-tuc', 'index')->name('index');
        Route::get('/{slug}', function ($slug) {
            $postCategory = \App\Models\PostCategory::where('slug', $slug)->first();
            if ($postCategory) {
                return App::make(App\Http\Controllers\Post\PostController::class)->category($slug);
            }

            $post = \App\Models\Post::where('slug', $slug)->first();
            if ($post) {
                return App::make(App\Http\Controllers\Post\PostController::class)->detail($slug);
            }

            abort(404);
        })->name('fallback');
    });
