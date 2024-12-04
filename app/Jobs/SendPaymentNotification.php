<?php

namespace App\Jobs;

use App\Events\SendPaymentNotificationEvent;
use App\Helpers\MessageHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$message)
    {
        //
        $this->message=$message;
        $this->user=$user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        //
        //$message = MessageHelper::getMessage('add_score', $variables);
        broadcast(new SendPaymentNotificationEvent($this->user,$this->message));
    }
}
