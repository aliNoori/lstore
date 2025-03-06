<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        //return parent::toArray($request);
        return [
            'rating'=>$this->rating,
            'review'=>$this->review,
            'user' =>$this->user? new UserResource($this->user):null,
            //'product' => new UserResource($this->whenLoaded('product'))
        ];
    }
}
