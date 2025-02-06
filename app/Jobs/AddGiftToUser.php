<?php
namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddGiftToUser implements ShouldQueue
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

        $isFirstOrder = !Order::where('user_id', $user->id)->where('id', '!=', $this->orderId)->exists();

        if ($isFirstOrder) {
            AddUserToLottery::dispatch('first_order', $this->orderId)
                ->onQueue('lottery')->onQueue('AddUserToLottery');
            Log::info("Added user {$user->username} to lottery and applied discount.");
        } else {
            Log::info("Order {$this->orderId} is not the first order for user {$user->username}.");
        }
    }
}
