<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        //return parent::toArray($request);
        return [
            'rating'=>$this->rating,
            'review'=>$this->review,
            'user' => new UserResource($this->whenLoaded('user')),
            //'product' => new UserResource($this->whenLoaded('product'))
        ];
    }
}
