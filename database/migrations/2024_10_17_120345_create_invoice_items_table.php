<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade'); // ارتباط با جدول invoices
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // ارتباط با جدول products
            $table->integer('quantity'); // تعداد محصول
            $table->decimal('price', 10, 2); // قیمت واحد
            $table->decimal('discount', 5, 2); // درصد تخفیف (مثال: 10.00 برای 10%)

            // قیمت نهایی واحد پس از اعمال تخفیف: price * (1 - (discount / 100))
            $table->decimal('price_with_discount', 10, 2); // قیمت پس از اعمال تخفیف

            // مبلغ کل برای این آیتم (quantity * price_with_discount)
            $table->decimal('total', 10, 2);

            $table->text('description')->nullable(); // توضیحات
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
