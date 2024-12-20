<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quantity',
    ];
    public function cartItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {

        return $this->hasMany(CartItem::class);
    }
}
