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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // شناسه یکتا
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // شناسه دسته‌بندی مرتبط
            $table->string('name')->unique(); // نام محصول، منحصر به فرد
            $table->text('description')->nullable(); // توضیحات محصول
            $table->decimal('price', 10, 2); // قیمت محصول
            $table->decimal('discount', 10, 2)->default(0.00); // تخفیف
            $table->integer('stock')->default(0); // موجودی کالا
            $table->string('sku')->unique()->nullable(); // شماره سریال محصول، منحصر به فرد و اختیاری
            $table->boolean('is_active')->default(true); // وضعیت فعال بودن محصول
            $table->date('expiration_date')->nullable(); // تاریخ انقضای محصول
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
