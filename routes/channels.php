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
    \Illuminate\Support\Facades\Log::info('CHANNEL',[$user]);
    return true; // دسترسی باز
});
Broadcast::channel('private-order-{orderId}', function ($user, $orderId) {
    // بررسی دسترسی کاربر
    \Illuminate\Support\Facades\Log::info('CHANNEL',[$user]);
    return $user->id === \App\Models\Order::find($orderId)->user_id;
});

