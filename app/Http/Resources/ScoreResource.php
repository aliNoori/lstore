<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
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
            'score'=>$this->score,
            'reason'=>$this->reason,
            'description'=>$this->description,
            'created_at'=>$this->created_at
        ];
    }
}
