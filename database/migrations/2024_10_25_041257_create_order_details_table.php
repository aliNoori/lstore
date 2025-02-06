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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // ارتباط با جدول orders
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // ارتباط با جدول products
            $table->integer('quantity'); // تعداد محصول
            $table->decimal('price', 10, 2); // قیمت واحد محصول در زمان سفارش
            $table->decimal('total', 10, 2); // مبلغ کل برای این آیتم (quantity * price)
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
        Schema::dropIfExists('order_details');
    }
};
