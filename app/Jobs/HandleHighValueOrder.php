<?php
namespace App\Jobs;

use App\Events\HandleHighValueOrderEvent;
use App\Helpers\MessageHelper;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleHighValueOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::find($this->orderId);
        $user = $order->user;

        // دلیل اضافه کردن کاربر به قرعه‌کشی
        $reason = 'high_value_order';

        // ارسال تسک به صف قرعه‌کشی
        AddUserToLottery::dispatch($reason, $this->orderId)->onQueue('AddUserToLottery');

        $variables = [
            'user_name' => $user->name,
            'order_number'=>$order->order_number,
            'charge_amount'=>1000,
        ];

        $message = MessageHelper::getMessage('handle_high_value_order', $variables);

        broadcast(new HandleHighValueOrderEvent($user,$message));
    }
}
