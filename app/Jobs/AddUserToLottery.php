<?php
namespace App\Jobs;

use App\Events\AddUserToLatteryEvent;
use App\Helpers\MessageHelper;
use App\Models\Order;
use App\Models\Lottery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddUserToLottery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reason, $orderId;

    public function __construct($reason, $orderId)
    {
        $this->reason = $reason;
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::find($this->orderId);
        $user = $order->user;

        Lottery::create(['user_id' => $user->id, 'order_id' => $this->orderId, 'reason' => $this->reason]);

        $variables = [
            'user_name' => $user->name,
        ];

        $message = MessageHelper::getMessage('add_user_to_lattery', $variables);

        broadcast(new AddUserToLatteryEvent($user,$message));
    }
}

