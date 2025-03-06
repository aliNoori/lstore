<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;

    public function __construct($user,$message)
    {
        $this->user = $user;
        $this->message=$message;
    }

    public function broadcastOn(): Channel|PrivateChannel
    {
        Log::info('$user',[$this->user]);
        Log::info('BroadcastOn called'.'sendPaymentNotificationEvent-'.$this->user->id);
        return new PrivateChannel('send-payment-notification-'.$this->user->id);
    }

    public function broadcastAs(): string
    {
        Log::info('BroadcastAs called');
        return 'SendPaymentNotification';
    }
    public function broadcastWith(): array
    {
        Log::info('Broadcasting data:', ['message' => $this->message]);

        return [
            'message' =>$this->message
        ];
    }

}
