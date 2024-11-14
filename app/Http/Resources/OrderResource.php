<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'total_amount' => $this->total_amount,
            'user_id' => $this->user_id,
            'address' => new AddressResource($this->address), // Pass the relationship, not just the ID
            'shipping_method' => new ShippingMethodResource($this->shippingMethod), // Same here
            'payment_method' => new PaymentMethodResource($this->paymentMethod), // And here as well
            'items'=>$this->orderDetails,
            'created_at'=>$this->created_at
        ];
    }
}
