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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // اضافه کردن ستون cart_id به عنوان کلید خارجی برای جدول carts
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');

            // اضافه کردن ستون product_id به عنوان کلید خارجی برای جدول products
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // ستون تعداد آیتم‌های محصول
            $table->integer('quantity')->default(1);

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
        Schema::dropIfExists('cart_items');
    }
};
