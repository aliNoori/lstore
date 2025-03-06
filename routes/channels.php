<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('chat', function ($user) {
    return true; // دسترسی باز
});
Broadcast::channel('order-{orderId}', function ($user, $orderId) {
    // بررسی دسترسی کاربر
    return $user->id === \App\Models\Order::find($orderId)->user_id;
});
Broadcast::channel('add-score-{userId}', function ($user, $userId) {
    // بررسی دسترسی کاربر
    \Illuminate\Support\Facades\Log::info('(int)$user->id,(int)$userId',[(int)$user->id,(int)$userId]);
    return (int)$user->id === (int)$userId;
});
Broadcast::channel('apply-coupon-{userId}', function ($user, $userId) {
    // بررسی دسترسی کاربر
    return (int)$user->id === (int)$userId;
});
Broadcast::channel('add-user-to-lottery-{userId}', function ($user, $userId) {
    // بررسی دسترسی کاربر
    return (int)$user->id === (int)$userId;
});
Broadcast::channel('charge-wallet-{userId}', function ($user, $userId) {
    // بررسی دسترسی کاربر
    return (int)$user->id === (int)$userId;
});
Broadcast::channel('handle-high-value-order-{userId}', function ($user, $userId) {
    // بررسی دسترسی کاربر
    return (int)$user->id === (int)$userId;
});
Broadcast::channel('send-payment-notification-{userId}', function ($user, $userId) {
    // بررسی دسترسی کاربر
    //return (int)$user->id === (int)$userId;
    return true;
});

