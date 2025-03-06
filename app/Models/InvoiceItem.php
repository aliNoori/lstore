<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable=['invoice_id','product_id','quantity','price','discount','total','price_with_discount','description'];


    protected static function boot()
    {
        parent::boot();

        // رویداد 'saving' برای محاسبه مجموع قیمت قبل از ذخیره‌سازی
        static::saving(function ($invoiceItem) {
            // محاسبه مجموع قیمت
            $invoiceItem->total = $invoiceItem->quantity * $invoiceItem->price;
        });
    }

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
