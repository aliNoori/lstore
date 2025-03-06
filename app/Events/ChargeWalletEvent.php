<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChargeWalletEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;

    public function __construct($user,$message)
    {
        $this->user = $user;
        $this->message=$message;
        $this->user->notifications()->create([
            'user_id' => $this->user->id,
            'message' => $this->message,
            'is_read' => false,
        ]);
    }

    public function broadcastOn(): Channel|PrivateChannel
    {
        Log::info('BroadcastOn called'.'chargeWallet-'.$this->user->id);
        return new PrivateChannel('charge-wallet-'.$this->user->id);
    }

    public function broadcastAs(): string
    {
        Log::info('BroadcastAs called');
        return 'ChargeWallet';
    }
    public function broadcastWith(): array
    {
        Log::info('Broadcasting data:', ['message' => $this->message]);

        return [
            'message' =>$this->message
        ];
    }

}
