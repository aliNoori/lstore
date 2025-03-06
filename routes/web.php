<?php

use Illuminate\Support\Facades\DB;
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

