<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;  // غیرفعال کردن wrap
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
            'email'=>$this->email,
            'image'=>new FileResource($this->image),
            'orders_count'=>$this->orders_count,
            'coupons_count'=>$this->coupons_count,
            'items_cart'=>$this->items_cart,
            'wallet_balance'=>$this->wallet_balance,
            'score' =>$this->scores,
            'roles'=>$this->user_roles
        ];
    }
}
