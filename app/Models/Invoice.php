<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;


    protected $fillable=['order_id','invoice_number','status','due_date','issue_date',
        'total_amount','discount','tax','tax_rate','shipping_cost','sub_total_amount'];


    protected static function boot()
    {
        parent::boot();

        // استفاده از رویداد 'saving' برای محاسبات قبل از ذخیره
        static::saving(function ($invoice) {
            // مقداردهی اولیه به tax اگر NULL باشد
            if (is_null($invoice->tax)) {
                $invoice->tax = 0.00;
            }

            // مقداردهی اولیه به شماره فاکتور اگر هنوز تنظیم نشده باشد
            if (is_null($invoice->invoice_number)) {
                $today = Carbon::now()->format('Ymd');
                $lastInvoice = Invoice::where('invoice_number', 'like', $today . '-%')
                    ->orderBy('invoice_number', 'desc')
                    ->first();

                if ($lastInvoice) {
                    // استخراج شماره آخرین فاکتور
                    $lastNumber = (int)explode('-', $lastInvoice->invoice_number)[1];
                    $invoice->invoice_number = sprintf('%s-%04d', $today, $lastNumber + 1);
                } else {
                    // اولین شماره فاکتور در روز
                    $invoice->invoice_number = $today . '-0001';
                }
            }
        });
    }


    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {

        return $this->hasMany(InvoiceItem::class);
    }


}
