<?php

namespace App\Jobs;

use App\Events\ChargeWalletEvent;
use App\Helpers\MessageHelper;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ChargeWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param int $amount
     */
    public function __construct(int $userId, int $amount)
    {
        $this->userId = $userId;
        $this->amount=$amount;
        //$this->amount = str_replace(',', '', $amount); // حذف کاما از مبلغ
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            Log::info("Starting charge_wallet for user {$this->userId} with amount {$this->amount}");

            // پیدا کردن کاربر
            $user = User::findOrFail($this->userId);

            Log::info("User found: " . json_encode($user->toArray()));

            // بررسی یا ایجاد کیف پول برای کاربر
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
            Log::info("Wallet status: " . json_encode($wallet->toArray()));


            // محاسبه ۱۰٪ مبلغ
            $amountToAdd = bcmul($this->amount, '0.10', 2);

            // افزایش موجودی کیف پول
            $wallet->balance = bcadd($wallet->balance, $amountToAdd, 2);
            $wallet->save();

            $variables = [
                'user_name' => $user->name,
                'charge_amount'=>$this->amount,
            ];

            $message = MessageHelper::getMessage('charge_wallet', $variables);

            broadcast(new ChargeWalletEvent($user,$message));

            Log::info("Successfully charged wallet for user {$this->userId} with amount {$amountToAdd}");

        } catch (Exception $e) {
            Log::error("Error charging wallet for user {$this->userId}: {$e->getMessage()}");
            throw $e; // در صورت نیاز به ارسال خطا برای مدیریت مجدد
        }
    }
}
