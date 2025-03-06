<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id'=>$this->id,
            'gateway'=>$this->gateway,
            'type'=>$this->type,
            'description'=>$this->description,
            'is_active'=>$this->is_active,
            'terminal_id'=>$this->terminal_id,
            'wsdl'=>$this->wsdl,
            'wsdl_confirm'=>$this->wsdl_confirm,
            'wsdl_reverse'=>$this->wsdl_reverse,
            'wsdl_multiplexed'=>$this->wsdl_multiplexd,
            'payment_gateway'=>$this->payment_gateway,
            'image'=>new FileResource($this->image)
        ];
    }
}
