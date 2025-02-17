<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    //public static $wrap = null;  // غیرفعال کردن wrap
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image ? new FileResource($this->image) : null, // در صورت نبود تصویر، مقدار null
            'views' => $this->views_count ?? null, // در صورت نبود viewsCount، مقدار null
            'likes' => $this->likes_count ?? null, // در صورت نبود likesCount، مقدار null
            'histories' => $this->histories ? HistoryResource::collection($this->histories) : null, // در صورت نبود تاریخچه، مقدار null
            'discount' => $this->discount ?? null, // در صورت نبود تخفیف، مقدار null
            'reviews' => $this->reviews ? ReviewResource::collection($this->reviews) : null, // در صورت نبود بررسی‌ها، مقدار null
            'reviewsCount'=>$this->reviews_count ??null,
            'category'=>new CategoryResource($this->category),
            'description'=>$this->description
        ];
    }
}
