<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PaymentGatewayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

//
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
####test Socket###
Route::post('/socket',[UserController::class, 'testSocket']);
####
Route::group(['prefix' => 'user'], function () {
    // Other routes without middleware
    Route::post('/create', [UserController::class, 'create']);
    Route::post('/login', [AuthController::class, 'login']);
    // Middleware applied only to '/profile' and '/users' routes
    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [UserController::class, 'profile']);
        Route::get('/notifications', [UserController::class, 'userNotifications']);
        Route::get('/notification/{id}/changeStatus', [UserController::class, 'userNotificationChangeStatus']);
        /////////
        Route::group(['prefix' => 'my'], function () {

        Route::get('/wallet', [UserController::class, 'myWallet']);
        Route::get('/scores', [UserController::class, 'myScores']);
        Route::get('/coupons', [UserController::class, 'myCoupons']);
        Route::get('/orders', [UserController::class, 'myOrders']);

        });

        Route::get('/users/list', [UserController::class, 'usersList']);
        Route::get('/show/{id}', [UserController::class, 'show']);
        Route::post('/update/{id}', [UserController::class, 'update']);
        Route::delete('/delete/{id}', [UserController::class, 'delete']);
        /////address
        Route::get('/show/address/{address_id}', [UserController::class, 'addressShow']);
        Route::get('/addresses', [UserController::class, 'addresses']);
        Route::post('/add/address', [UserController::class, 'addAddress']);
        Route::post('/edit/address/{id}', [UserController::class, 'editAddress']);

        ///gateway
        Route::post('/manageSelectedPayment/{id}',[PaymentGatewayController::class,'manageGateway']);
        Route::post('/processPayment/{order_number}/{gateway_id}',[PaymentController::class,'processPayment']);
        Route::post('/processPayment/{order_number}/{wallet_id}/paymentWithWallet',[PaymentController::class,'paymentWithWallet']);
    });
});
/////////////////////////////======== Callback Payment ================////////////
Route::post('/callback/payment',[PaymentController::class,'callbackPayment']);

///////================   Product  =============///////////
use \App\Http\Controllers\ProductController;
Route::group(['prefix' => 'product'], function () {

    Route::get('/show/{id}', [ProductController::class, 'show']);
    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {
        Route::post('/create', [ProductController::class, 'create']);
        ///
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::delete('/delete/{id}', [ProductController::class, 'delete']);
        //////////////////
        Route::get('/like/{id}', [ProductController::class, 'like']);
        Route::get('/disLike/{id}', [ProductController::class, 'disLike']);
        Route::get('/view/{id}', [ProductController::class, 'view']);
        Route::get('/histories/{id}', [ProductController::class, 'history']);
        Route::post('/review/{id}', [ProductController::class, 'review']);

    });
    Route::get('/list', [ProductController::class, 'index']);
});

///////================   Cart  =============///////////
use \App\Http\Controllers\CartController;

Route::group(['prefix' => 'cart'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        Route::get('/items/show', [CartController::class, 'itemsShow']);
        Route::get('/items/{product_id}/info', [CartController::class, 'productInfo']);
        Route::post('/item/add/{id}', [CartController::class, 'addToCart']);
        Route::post('/item/remove/{id}', [CartController::class, 'removeFromCart']);

    });
});


///////================   Invoice  =============///////////
///
use \App\Http\Controllers\InvoiceController;


Route::group(['prefix' => 'invoice'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        Route::get('/create/{order_number}', [InvoiceController::class, 'create']);
        Route::get('/show', [InvoiceController::class, 'show']);
        Route::post('/update/{id}', [InvoiceController::class, 'update']);
        Route::post('/delete/{id}', [InvoiceController::class, 'delete']);
    });
});

///////================   Category  =============///////////
///
use \App\Http\Controllers\CategoryController;

Route::group(['prefix' => 'category'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {
        Route::get('/list',[CategoryController::class, 'list']);
        Route::post('/create', [CategoryController::class, 'create']);
        Route::get('/show/{id}', [CategoryController::class, 'show']);
        Route::post('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
    });
});


///////================   ShippingMethod  =============///////////
///
use \App\Http\Controllers\ShippingMethodController;

Route::group(['prefix' => 'shippingMethod'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        Route::post('/create', [ShippingMethodController::class, 'create']);
        Route::get('/list', [ShippingMethodController::class, 'list']);
        Route::get('/show/{id}', [ShippingMethodController::class, 'show']);
        Route::post('/update/{id}', [ShippingMethodController::class, 'update']);
        Route::delete('/delete/{id}', [ShippingMethodController::class, 'delete']);
    });
});

///////================   PaymentMethod  =============///////////
///
use \App\Http\Controllers\PaymentMethodController;

Route::group(['prefix' => 'paymentMethod'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        Route::post('/create', [PaymentMethodController::class, 'create']);
        Route::get('/list', [PaymentMethodController::class, 'list']);
        Route::get('/show/{id}', [PaymentMethodController::class, 'show']);
        Route::post('/update/{id}', [PaymentMethodController::class, 'update']);
        Route::delete('/delete/{id}', [PaymentMethodController::class, 'delete']);
    });
});

///////================   PaymentMethod  =============///////////
///


Route::group(['prefix' => 'onlineMethodGateway'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        Route::post('/create', [PaymentGatewayController::class, 'create']);
        Route::get('/list', [PaymentGatewayController::class, 'list']);
        Route::get('/show/{id}', [PaymentGatewayController::class, 'show']);
        Route::post('/update/{id}', [PaymentGatewayController::class, 'update']);
        Route::delete('/delete/{id}', [PaymentGatewayController::class, 'delete']);
    });
});

///////================   Order  =============///////////

use App\Http\Controllers\OrderController;

Route::group(['prefix' => 'user'], function () {

    Route::get('/orders', [OrderController::class, 'showOrders']);

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        // نمایش لیست سفارش‌های کاربر (سفارش‌های فعلی کاربر لاگین‌شده)
        Route::get('/myOrders', [OrderController::class, 'myOrders']);
// ایجاد سفارش جدید
        Route::post('/create/order/{id}', [OrderController::class, 'createOrder']);
        // نمایش جزئیات یک سفارش خاص با استفاده از شناسه سفارش
        Route::get('/order/{id}', [OrderController::class, 'showOrder']);
        //add shipping method
        Route::post('addShipping/{shipping_id}/order/{order_number}', [OrderController::class, 'addShippingToOrder']);
        // ویرایش سفارش (در صورتی که هنوز نهایی نشده باشد)
        Route::put('/order/{id}/edit', [OrderController::class, 'editOrder']);
// لغو سفارش (در صورتی که هنوز ارسال نشده باشد)
        Route::delete('/order/{id}/cancel', [OrderController::class, 'cancelOrder']);
        // پیگیری وضعیت سفارش
        Route::get('/order/{id}/status', [OrderController::class, 'trackOrderStatus']);

    });
});

///////================   Permission Role Setting  =============///////////
///
///
use \App\Http\Controllers\PermissionRoleController;

Route::group(['prefix' => 'permissionRoleSetting'], function () {

    Route::middleware(['auth:sanctum'/*, 'has.permission', 'has.role'*/])->group(function () {

        Route::post('/togglePermissions/{email}', [PermissionRoleController::class, 'togglePermissions']);
        Route::post('/toggleRoles/{email}', [PermissionRoleController::class, 'toggleRoles']);

    });
});


