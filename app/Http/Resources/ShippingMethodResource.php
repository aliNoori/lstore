<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
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
            'name'=>$this->name,
            'description'=>$this->description,
            'cost'=>$this->cost,
            'delivery_time'=>$this->delivery_time,
            'is_active'=>$this->is_active,
            'image'=>new FileResource($this->image)
        ];
    }
}
