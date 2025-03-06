<?php
namespace App\Jobs;

use App\Events\ApplyCouponEvent;
use App\Helpers\MessageHelper;
use App\Models\Order;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApplyCoupon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $code;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        // تولید کد کوپن منحصربه‌فرد
        $code = 'XMAS' . Carbon::now()->format('Y') . '-' . Str::upper(Str::random(6));
        $this->code = $code;
    }

    public function handle()
    {
        $order = Order::find($this->orderId);
        $user = $order->user;

        // محاسبه تاریخ انقضای کوپن
        $expireDate = now()->addDays(30);

        // ایجاد کوپن با تاریخ انقضا
        Coupon::create([
            'user_id' => $user->id,
            'code' => $this->code,
            'expire_date' => $expireDate,
        ]);

        Log::info("Coupon {$this->code} applied to user {$user->username} for order {$this->orderId}.");

        $variables = [
            'user_name' => $user->name,
            'coupon_code'=>$this->code,
        ];

        $message = MessageHelper::getMessage('apply_coupon', $variables);

        broadcast(new ApplyCouponEvent($user,$message));
    }
}
