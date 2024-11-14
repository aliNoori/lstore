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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade'); // اتصال به جدول سفارشات
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('cascade');  // اتصال به کیف پول، در صورت پرداخت با کیف پول
            $table->string('transaction_type', 20); /*("payment","refund","deposit","withdrawal","fee")*/// نوع تراکنش
            $table->string('payment_method', 50); // روش پرداخت (مثلاً "credit_card", "wallet", "bank_transfer")

            $table->decimal('amount', 15, 2); // مبلغ تراکنش
            $table->string('status', 20)->default('pending'); // وضعیت تراکنش

            // فیلدهای جدید مشابه مدل Django
            $table->string('token', 50)->nullable();  // توکن تراکنش
            // فیلدهای مربوط به پرداخت با کردیت کارت
            $table->string('card_number_hash', 100)->nullable(); // شماره کارت هش شده
            $table->string('rrn', 50)->nullable();  // شماره پیگیری (RRN)
            $table->string('terminal_no', 50)->nullable();  // شماره ترمینال


            $table->string('tsp_token', 50)->nullable();  // توکن TSP
            $table->decimal('sw_amount', 15, 2)->nullable();  // مبلغ نهایی پرداخت شده
            $table->string('strace_no', 50)->nullable();  // شماره ردیابی
            $table->string('redirect_url', 200)->nullable();  // آدرس بازگشت
            $table->string('callback_error', 200)->nullable();  // خطای بازگشت
            $table->string('verify_error', 200)->nullable();  // خطای تایید
            $table->string('reverse_error', 200)->nullable();  // خطای بازگشت وجه

            $table->timestamps();  // تاریخ ایجاد و به‌روزرسانی تراکنش
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
