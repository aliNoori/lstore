<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\InvoiceItemResource;

class InvoiceResource extends JsonResource
{
    public static $wrap = null;  // غیرفعال کردن wrap
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        //return parent::toArray($request);
        return [
            'id'=>$this->id,
            'order_id'=>$this->order_id,
            'invoice_number'=>$this->invoice_number,
            'status'=>$this->status,
            'due_date'=>$this->due_date,
            'issue_date'=>$this->issue_date,
            'tax_rate'=>$this->tax_rate,
            'sub_total_amount'=>$this->sub_total_amount,
            'tax'=>$this->tax,
            'shipping_cost'=>$this->shipping_cost,
            'total_amount'=>$this->total_amount,
            'items'=>InvoiceItemResource::collection($this->items),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,

        ];
    }
}
