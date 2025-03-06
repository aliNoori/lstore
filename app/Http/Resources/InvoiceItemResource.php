<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
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
            'product'=>new ProductResource($this->product), // نمایش اطلاعات محصول مرتبط
            'quantity'=>$this->quantity,
            'price'=>$this->price,
            'total'=>$this->total,
            'discount'=>$this->discount,
            'price_with_discount'=>$this->price_with_discount,
            'description'=>$this->description,
        ];
    }
}
