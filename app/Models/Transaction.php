<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable=['order_id','wallet_id','transaction_type','payment_method','amount','status','token',
        'card_number_hash','rrn','terminal_no','tsp_token','sw_amount','strace_no','redirect_url',
        'callback_error','verify_error','reverse_error'];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(order::class);
    }
}
