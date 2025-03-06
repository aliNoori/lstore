<?php

namespace App\Models;

use App\Traits\ImageManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PaymentGateway extends Model
{
    use HasFactory,ImageManager;

    protected $fillable = [
        'gateway',
        'type',
        'description',
        'is_active',
        'terminal_id',
        'wsdl',
        'wsdl_confirm',
        'wsdl_reverse',
        'wsdl_multiplexed',
        'payment_gateway',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function image():MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
