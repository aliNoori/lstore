<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_id;

    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    public function broadcastOn(): Channel|PrivateChannel
    {
        Log::info('BroadcastOn called'.'order-'.$this->order_id);
        return new PrivateChannel('order-'.$this->order_id);
    }

    public function broadcastAs(): string
    {
        Log::info('BroadcastAs called');
        return 'OrderCreatedPrivate';
    }
    public function broadcastWith(): array
    {
        Log::info('Broadcasting data:', ['order_id' => $this->order_id]);
        return [
            'order_id' => $this->order_id
        ];
    }

}
