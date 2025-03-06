<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleHighValueOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $message;

    public function __construct($order,$message)
    {
        $this->order = $order;
        $this->message=$message;
        $this->order->user->notifications()->create([
            'user_id' => $this->order->user->id,
            'message' => $this->message,
            'is_read' => false,
        ]);
    }

    public function broadcastOn(): Channel|PrivateChannel
    {
        Log::info('BroadcastOn called'.'handleHighValueOrderEvent-'.$this->order->id);
        return new PrivateChannel('handle-high-value-order-'.$this->order->id);
    }

    public function broadcastAs(): string
    {
        Log::info('BroadcastAs called');
        return 'HandleHighValueOrder';
    }
    public function broadcastWith(): array
    {
        Log::info('Broadcasting data:', ['message' => $this->message]);

        return [
            'message' =>$this->message
        ];
    }

}
