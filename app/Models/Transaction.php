<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable=['order_id','wallet_id','transaction_type','payment_method','amount','status','token',
        'card_number_hash','rrn','terminal_no','tsp_token','sw_amount','strace_no','redirect_url',
        'callback_error','verify_error','reverse_error'];

    public function paymentMethod(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
