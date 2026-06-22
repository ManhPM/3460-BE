<?php

use App\Http\Controllers\LogViewerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Admin\Http\Controllers\Home\HomeController::class, 'index']);

// login
Route::controller(App\Admin\Http\Controllers\Auth\LoginController::class)
    ->middleware('guest:admin')
    ->prefix('/login')
    ->as('login.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/not-found', 'notfound')->name('notfound');
        Route::post('/', 'login')->name('post');
    });

Route::group(['middleware' => 'admin.auth.admin:admin'], function () {

    Route::prefix('logs')->name('logs.')->group(function () {
        Route::group(['middleware' => ['auth:admin']], function () {
            // Trang chủ - hiển thị danh sách file log
            Route::get('/', [LogViewerController::class, 'index'])->name('index');

            // Chi tiết file log theo ngày
            Route::get('/detail/{date}', [LogViewerController::class, 'show'])
                ->name('show');

            // Download file log
            Route::get('/download', [LogViewerController::class, 'download'])->name('download');

            // Clear file log
            Route::delete('/clear', [LogViewerController::class, 'clear'])->name('clear');
        });
    });

    Route::prefix('/membership-levels')->as('membership_level.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\MembershipLevel\MembershipLevelController::class)->group(function () {
            Route::group(['middleware' => ['permission:createMembershipLevel', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewMembershipLevel', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateMembershipLevel', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteMembershipLevel', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    Route::prefix('/commission_withdrawals')->as('commission_withdrawal.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\CommissionWithdrawal\CommissionWithdrawalController::class)->group(function () {
            Route::group(['middleware' => ['permission:viewOrder', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateOrder', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteOrder', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    Route::prefix('/banks')->as('bank.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Bank\BankController::class)->group(function () {
            Route::group(['middleware' => ['permission:viewBank', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::get('/them/{id}', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:updateBank', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });
        });
    });

    //Section
    Route::prefix('/sections')->as('section.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Section\SectionController::class)->group(function () {
            Route::group(['middleware' => ['permission:createSection', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewSection', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateSection', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteSection', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //Notification
    Route::prefix('/notifications')->as('notification.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Notification\NotificationController::class)->group(function () {
            Route::group(['middleware' => ['permission:createNotification', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewNotification', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/admin', 'getAllNotificationAdmin')->name('getAllNotificationAdmin');
                Route::get('/admin-view', 'getAllNotificationAdminView')->name('getAllNotificationAdminView');
                Route::post('/update-device-token', 'updateDeviceToken')->name('updateDeviceToken');
                Route::get('/sua/{id}', 'edit')->name('edit');

                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/read-all', 'readAllNotification')->name('readAllNotification');
            });

            Route::group(['middleware' => ['permission:updateNotification', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteNotification', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
                Route::post('/xu-ly-nhieu-ban-ghi', 'actionMultipleRecord')->name('multiple');
            });
        });
    });

    //Shipping Rate
    Route::controller(App\Admin\Http\Controllers\ShippingRate\ShippingRateController::class)
        ->prefix('/shipping_rates')
        ->as('shipping_rate.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createShippingRates', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewShippingRates', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateShippingRates', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteShippingRates', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    //Voucher
    Route::controller(App\Admin\Http\Controllers\Voucher\VoucherController::class)
        ->prefix('/vouchers')
        ->as('voucher.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createVoucher', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewVoucher', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateVoucher', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteVoucher', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    //Voucher Program
    Route::controller(App\Admin\Http\Controllers\VoucherProgram\VoucherProgramController::class)
        ->prefix('/voucher_programs')
        ->as('voucher_program.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createVoucherProgram', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewVoucherProgram', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::post('/give', 'giveVoucher')->name('giveVoucher');
                Route::post('/reset', 'reset')->name('reset');
            });

            Route::group(['middleware' => ['permission:updateVoucherProgram', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteVoucherProgram', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    //Discount
    Route::controller(App\Admin\Http\Controllers\Discount\DiscountController::class)
        ->prefix('/discounts')
        ->as('discount.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createDiscountCode', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewDiscountCode', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateDiscountCode', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteDiscountCode', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    //FlashSale
    Route::controller(App\Admin\Http\Controllers\FlashSale\FlashSaleController::class)
        ->prefix('/flash-sales')
        ->as('flashsale.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createFlashSale', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewFlashSale', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateFlashSale', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::get('/add-product', 'addProduct')->name('add_product');

            Route::group(['middleware' => ['permission:deleteFlashSale', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
                Route::delete('/xoa-detail/{id?}', 'deleteDetail')->name('deleteDetail');
            });
        });

    //Review
    Route::controller(App\Admin\Http\Controllers\Review\ReviewController::class)
        ->prefix('/reviews')
        ->as('review.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createReview', 'auth:admin']], function () {
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewReview', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
            });

            Route::group(['middleware' => ['permission:deleteReview', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    //admin
    Route::prefix('/admins')->as('admin.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Admin\AdminController::class)->group(function () {
            Route::group(['middleware' => ['permission:createAdmin', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewAdmin', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateAdmin', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteAdmin', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //role
    Route::prefix('/role')->as('role.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Role\RoleController::class)->group(function () {

            Route::group(['middleware' => ['permission:createRole', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewRole', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateRole', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteRole', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    Route::prefix('/posts')->as('post.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Post\PostController::class)->group(function () {

            Route::group(['middleware' => ['permission:createPost', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewPost', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updatePost', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deletePost', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    Route::prefix('/posts-categories')->as('post_category.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\PostCategory\PostCategoryController::class)->group(function () {
            Route::group(['middleware' => ['permission:createPostCategory', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewPostCategory', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updatePostCategory', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deletePostCategory', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    Route::controller(App\Admin\Http\Controllers\Setting\SettingController::class)
        ->prefix('/settings')
        ->as('setting.')
        ->group(function () {
            Route::group(['middleware' => ['permission:settingGeneral', 'auth:admin']], function () {
                Route::get('/general', 'general')->name('general');
                Route::get('/footer', 'footer')->name('footer');
                Route::get('/contact', 'contact')->name('contact');
                Route::get('/information', 'information')->name('information');
                Route::get('/theme', 'theme')->name('theme');
                Route::get('/mobile', 'mobile')->name('mobile');
                Route::get('/web-theme', 'webTheme')->name('webTheme');
                Route::get('/config', 'config')->name('config');
                Route::get('/zalo-miniapp-config', 'zaloMiniAppConfig')->name('zaloMiniAppConfig');
                Route::get('/membership-level', 'membershipLevel')->name('membershipLevel');
            });
            Route::put('/update', 'update')->name('update');
        });

    Route::prefix('/sliders')->as('slider.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Slider\SliderItemController::class)
            ->as('item.')
            ->group(function () {
                Route::get('/{slider_id}/item/them', 'create')->name('create');
                Route::get('/{slider_id}/item', 'index')->name('index');
                Route::get('/item/sua/{id}', 'edit')->name('edit');
                Route::put('/item/sua', 'update')->name('update');
                Route::post('/item/them', 'store')->name('store');
                Route::delete('/{slider_id}/item/xoa/{id}', 'delete')->name('delete');
            });
        Route::controller(App\Admin\Http\Controllers\Slider\SliderController::class)->group(function () {
            Route::group(['middleware' => ['permission:createSlider', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewSlider', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateSlider', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteSlider', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //Order detail
    Route::controller(App\Admin\Http\Controllers\Order\OrderDetailController::class)
        ->prefix('order-detail')
        ->as('order_detail.')
        ->group(function () {
            Route::delete('/delete/{id?}', 'delete')->name('delete');
        });

    //Order
    Route::prefix('/orders')->as('order.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Order\OrderController::class)->group(function () {
            Route::group(['middleware' => ['permission:createOrder', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });

            Route::group(['middleware' => ['permission:viewOrder', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/affiliate', 'affiliate')->name('affiliate');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });


            Route::group(['middleware' => ['permission:updateOrder', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteOrder', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });

            Route::get('/render-info-shipping', 'renderInfoShipping')->name('render_info_shipping');
            Route::get('/confirm/{id?}', 'confirm')->name('confirm');
            Route::get('/cancel/{id?}', 'cancel')->name('cancel');
            Route::get('/add-product', 'addProduct')->name('add_product');
            Route::get('/calculate-total-before-save-order', 'calculateTotalBeforeSaveOrder')->name('calculate_total_before_save_order');
        });
    });

    //attributes
    Route::prefix('/attributes')->as('attribute.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\AttributeVariation\AttributeVariationController::class)
            ->as('variation.')
            ->group(function () {
                Route::get('/{attribute_id}/variations/them', 'create')->name('create');
                Route::get('/{attribute_id}/variations', 'index')->name('index');
                Route::get('/variations/sua/{id}', 'edit')->name('edit');
                Route::put('/variations/sua', 'update')->name('update');
                Route::post('/variations/them', 'store')->name('store');
                Route::delete('/{attribute_id}/variations/xoa/{id}', 'delete')->name('delete');
            });
        Route::controller(App\Admin\Http\Controllers\Attribute\AttributeController::class)->group(function () {
            Route::group(['middleware' => ['permission:createProductAttribute', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewProductAttribute', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateProductAttribute', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteProductAttribute', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //Product
    Route::prefix('/products')->as('product.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Product\ProductController::class)->group(function () {
            Route::group(['middleware' => ['permission:createProduct', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
                Route::get('/export', 'export')->name('export');
                Route::post('/import', 'import')->name('import');
                Route::get('/clear-all-data', 'clear')->name('clear');
                Route::get('/seed-data', 'seed')->name('seed');
                Route::get('/check-flash-sale/{id?}', 'checkProductFlashSale')->name('checkProductFlashSale');
            });
            Route::group(['middleware' => ['permission:viewProduct', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateProduct', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteProduct', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
        Route::controller(App\Admin\Http\Controllers\Product\ProductAttributeController::class)
            ->prefix('/attributes')
            ->as('attribute.')
            ->group(function () {
                Route::get('/them', 'create')->name('create');
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::put('/sua', 'update')->name('update');
                Route::post('/them', 'store')->name('store');
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        Route::controller(App\Admin\Http\Controllers\Product\ProductVariationController::class)
            ->prefix('/variations')
            ->as('variation.')
            ->group(function () {
                Route::get('/check', 'check')->name('check');
                Route::get('/them', 'create')->name('create');
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::put('/sua', 'update')->name('update');
                Route::post('/them', 'store')->name('store');
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
    });

    //Product category
    Route::prefix('/categories')->as('category.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\ProductCategory\ProductCategoryController::class)->group(function () {
            Route::group(['middleware' => ['permission:createProductCategory', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewProductCategory', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::get('/{id}/products', 'product')->name('product');
            });

            Route::group(['middleware' => ['permission:updateProductCategory', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteProductCategory', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //user
    Route::prefix('/users')->as('user.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\User\UserController::class)->group(function () {
            Route::group(['middleware' => ['permission:createUser', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewUser', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{userId}/point-earned-history', 'pointEarnedHistory')->name('point-earned-history');
                Route::get('/{userId}/point-used-history', 'pointUsedHistory')->name('point-used-history');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateUser', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteUser', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //user
    Route::prefix('/user-addresses')->as('user_address.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\UserAddress\UserAddressController::class)->group(function () {
            Route::group(['middleware' => ['permission:createUser', 'auth:admin']], function () {
                Route::get('/them/{id}', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewUser', 'auth:admin']], function () {
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateUser', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
                Route::get('/set-default/{id}', 'setDefault')->name('setDefault');
            });

            Route::group(['middleware' => ['permission:deleteUser', 'auth:admin']], function () {
                Route::get('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    Route::prefix('/quyen')->as('permission.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Permission\PermissionController::class)->group(function () {
            Route::get('/them', 'create')->name('create');
            Route::get('/', 'index')->name('index');
            Route::get('/sua/{id}', 'edit')->name('edit');
            Route::put('/sua', 'update')->name('update');
            Route::post('/them', 'store')->name('store');
            Route::post('/multiple', 'actionMultipleRecord')->name('multiple');
            Route::delete('/xoa/{id}', 'delete')->name('delete');
        });
    });

    //module
    Route::prefix('/module')->as('module.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Module\ModuleController::class)->group(function () {
            Route::get('/them', 'create')->name('create');
            Route::get('/', 'index')->name('index');
            Route::get('/summary', 'summary')->name('summary');
            Route::get('/sua/{id}', 'edit')->name('edit');
            Route::put('/sua', 'update')->name('update');
            Route::post('/them', 'store')->name('store');
            Route::post('/multiple', 'actionMultipleRecord')->name('multiple');
            Route::delete('/xoa/{id}', 'delete')->name('delete');
        });
    });

    //ckfinder
    Route::prefix('/quan-ly-file')->as('ckfinder.')->group(function () {
        Route::any('/ket-noi', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
            ->name('connector');
        Route::any('/duyet', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
            ->name('browser');
    });
    Route::get('/dashboard', [App\Admin\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pending-orders', [App\Admin\Http\Controllers\Dashboard\DashboardController::class, 'getPendingOrders'])->name('dashboard.pending_orders');

    //auth
    Route::controller(App\Admin\Http\Controllers\Auth\ProfileController::class)
        ->prefix('/profile')
        ->as('profile.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });

    Route::controller(App\Admin\Http\Controllers\Auth\ChangePasswordController::class)
        ->prefix('/password')
        ->as('password.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });
    Route::prefix('/search')->as('search.')->group(function () {
        Route::prefix('/select')->as('select.')->group(function () {
            Route::get('/icon', [App\Admin\Http\Controllers\Icon\IconSearchSelectController::class, 'selectSearch'])->name('icon');
            Route::get('/user', [App\Admin\Http\Controllers\User\UserSearchSelectController::class, 'selectSearch'])->name('user');
            Route::get('/product', [App\Admin\Http\Controllers\Product\ProductSearchSelectController::class, 'selectSearch'])->name('product');
            Route::get('/discount', [App\Admin\Http\Controllers\Discount\DiscountSearchSelectController::class, 'selectSearch'])->name('discount');
            Route::get('/voucher', [App\Admin\Http\Controllers\Voucher\VoucherSearchSelectController::class, 'selectSearch'])->name('voucher');
            Route::get('/admin-branch', [App\Admin\Http\Controllers\Admin\AdminBranchSearchSelectController::class, 'selectSearch'])->name('admin_branch');
        });
        Route::get('/render-product-and-variation', [App\Admin\Http\Controllers\Product\ProductController::class, 'searchRenderProductAndVariationOrder'])->name('render_product_and_variation');
        Route::get('/render-product', [App\Admin\Http\Controllers\Product\ProductController::class, 'searchRenderProductFlashSale'])->name('render_product_flash_sale');
    });

    Route::post('/logout', [App\Admin\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');

    // Wallet Transaction
    Route::prefix('/wallet_transactions')->as('wallet_transaction.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Wallet\WalletTransactionController::class)->group(function () {
            Route::group(['middleware' => ['auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/xem/{id}', 'show')->name('show');
                Route::post('/approve/{id}', 'approve')->name('approve');
                Route::post('/reject/{id}', 'reject')->name('reject');
            });
        });
    });

    // Branch inventories
    Route::prefix('/inventories')->as('inventory.')->group(function () {
        Route::get('/', [App\Admin\Http\Controllers\Inventory\InventoryController::class, 'index'])->name('index');
        Route::get('/data', [App\Admin\Http\Controllers\Inventory\InventoryController::class, 'data'])->name('data');
        Route::post('/update-qty', [App\Admin\Http\Controllers\Inventory\InventoryController::class, 'updateQty'])->name('update_qty');
        Route::post('/import-excel', [App\Admin\Http\Controllers\Inventory\InventoryController::class, 'importFromExcel'])->name('import_excel');
    });
});

Route::prefix('/search')->as('search.')->group(function () {
    Route::prefix('/select')->as('select.')->group(function () {
        Route::get('/province', [App\Admin\Http\Controllers\Province\ProvinceSearchSelectController::class, 'selectSearch'])->name('province');
        Route::get('/ward', [App\Admin\Http\Controllers\Ward\WardSearchSelectController::class, 'selectSearch'])->name('ward');
    });
});
