<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/health-check', function () {
    try {
        // بررسی اتصال به پایگاه داده
        //DB::connection()->getPdo();

        // بازگشت پاسخ موفقیت
        return response()->json(['status' => 'OK'], 200);
    } catch (\Exception $e) {
        // بازگشت پاسخ خطا
        return response()->json(['status' => 'Error', 'message' => $e->getMessage()], 500);
    }
});
############ Verify Email ################
Route::get('/email/verify',[EmailVerificationController::class,'show'])
    ->middleware('auth')
    ->name('verification.notice');
Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

########### Use Middleware verified #######
Route::get('/profile', function () {
    // Only verified users may access this route...
})->middleware(['auth', 'verified']);

