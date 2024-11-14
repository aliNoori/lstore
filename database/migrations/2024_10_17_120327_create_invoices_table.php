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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // ارتباط با order
            $table->foreignId('order_id')
                ->nullable()  // مقدار null قابل قبول است
                ->constrained('orders') // جدول مرتبط به 'orders'
                ->onDelete('cascade'); // حذف آبشاری

            // شماره فاکتور منحصر به فرد
            $table->string('invoice_number')->unique();

            // تاریخ صدور فاکتور
            $table->timestamp('issue_date')->useCurrent(); // تنظیم تاریخ ایجاد به‌صورت خودکار

            // تاریخ سررسید
            $table->timestamp('due_date')->nullable(); // این فیلد باید توسط کاربر پر شود

            // مجموع مبلغ فاکتور
            $table->decimal('total_amount', 10, 2)->default(0.00); // مبلغ کل
            $table->decimal('sub_total_amount', 10, 2)->default(0.00); // مبلغ بدون مالیات

            // نرخ مالیات و مقدار مالیات
            $table->decimal('tax_rate', 10, 2)->default(0.00); // نرخ مالیات
            $table->decimal('tax', 10, 2)->default(0.00); // مالیات

            // وضعیت پرداخت
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

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
        Schema::dropIfExists('invoices');
    }
};
