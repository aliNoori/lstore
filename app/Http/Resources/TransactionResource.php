<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'order'=>$this->order,
            'transaction_type'=>$this->transaction_type,
            'payment_method'=>$this->payment_method,
            'amount'=>$this->amount,
            'status'=>$this->status,
            'token'=>$this->token,
            'card_number_hash'=>$this->card_number_hash,
            'rrn'=>$this->rrn,
            'terminal_no'=>$this->terminal_no,
            'tsp_token'=>$this->tsp_token,
            'sw_amount'=>$this->sw_amount,
            'strace_no'=>$this->strace_no,
            'redirect_url'=>$this->redirect_url,
            'callback_error'=>$this->callback_error,
            'verify_error'=>$this->verify_error,
            'reverse_error'=>$this->reverse_error,
            'date'=>$this->created_at
        ];
    }
}
