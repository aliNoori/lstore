<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //TODO:add order_number
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // شماره سفارش منحصر به فرد
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable(); // زمانی که سفارش تکمیل شده است
            $table->decimal('total_amount', 10, 2); // مبلغ کل سفارش
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ارتباط با جدول users
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('cascade'); // ارتباط با جدول addresses
            $table->foreignId('shipping_method_id')->nullable()->constrained()->onDelete('cascade'); // ارتباط با جدول shippingMethods
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('cascade'); // اتصال به جدول روش‌های پرداخت
            //$table->string('status')->default('pending'); // وضعیت سفارش (مثلاً pending, completed, cancelled)


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
        Schema::dropIfExists('orders');
    }
};
