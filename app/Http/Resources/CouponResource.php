<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        //return parent::toArray($request);
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'code'=>$this->code,
            'expire_date'=>$this->expire_date,
            'discount_amount'=>$this->discount_amount,
            'discount_type'=>$this->discount_type,
            'is_used'=>$this->is_used,
            'description'=>$this->description,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,

        ];
    }
}
