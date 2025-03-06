<?php

namespace App\Console\Commands;

use App\Jobs\SendPaymentNotification;
use App\Models\User; // فرض می‌کنیم که کاربر از مدل User گرفته می‌شود
use Illuminate\Console\Command;

class SendNotificationDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notification-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This sends notification daily for users';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // فرض می‌کنیم که شما می‌خواهید برای تمامی کاربران پیام بفرستید
        $users = User::all(); // یا می‌توانید محدودیتی برای کاربران داشته باشید

        foreach ($users as $user) {
            // ارسال کاربر به صف همراه با پیام
            SendPaymentNotification::dispatch($user, 'این یک پیام آزمایشی برای تست برنامه زمانبندی لاراول است که با سوکت ارسال می شود')
                ->onQueue('SendPaymentNotification');
        }

        $this->info('Notifications sent successfully to all users.');
        //return Command::SUCCESS;
    }
}
