<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'balance'=>$this->balance,
            'currency'=>$this->currency,
            'transactions'=>$this->transactions,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
