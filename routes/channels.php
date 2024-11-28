<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
\Illuminate\Support\Facades\Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    // اطلاعات درخواست برای دیباگ
    \Illuminate\Support\Facades\Log::info('Broadcasting auth hit', [
        'headers' => $request->headers->all(),
        'cookies' => $request->cookies->all(),
        'user' => $request->user(),
    ]);

    // چک کردن کاربر احراز هویت شده و بازگشت پاسخ
    if ($request->user()) {
        return response()->json(['status' => 'ok']);
    }

    return response()->json(['status' => 'unauthorized'], 403);
})->middleware('auth:sanctum');
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('chat', function ($user) {
    \Illuminate\Support\Facades\Log::info('CHANNEL');
    return true; // دسترسی باز
});

